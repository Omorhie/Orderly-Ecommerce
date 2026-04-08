<?php
session_start();

if(!isset($_SESSION['username_user'])){
    header("Location: ../auth/user/login.php");
    exit;
}

require_once "../config/database.php";
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE user_id='$user_id'
    ORDER BY order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History - Orderly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #0F2854;
            --secondary: #4988C4;
            --bg-color: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #22c55e;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR */
        .navbar {
            background: var(--white);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: var(--secondary);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
            font-weight: 600;
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary);
            border-radius: 2px;
        }

        .nav-right a {
            padding: 10px 24px;
            background: var(--primary);
            color: var(--white);
            border-radius: 99px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .nav-right a:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(73, 136, 196, 0.2);
        }

        /* CONTAINER */
        .container {
            max-width: 1200px;
            width: 95%;
            margin: 40px auto;
            flex: 1;
        }

        .page-title {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* TABLE WRAPPER */
        .table-wrapper {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--primary);
            color: var(--white);
        }

        th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        td {
            padding: 20px;
            text-align: left;
            color: var(--text-dark);
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .order-id {
            font-weight: 600;
            color: var(--text-gray);
        }

        .product-name {
            font-weight: 600;
            color: var(--primary);
            display: block;
            margin-bottom: 4px;
        }

        .order-address {
            font-size: 0.85rem;
            color: var(--text-gray);
            line-height: 1.4;
        }

        .method-badge {
            background: var(--bg-color);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--secondary);
            border: 1px solid rgba(73, 136, 196, 0.2);
        }

        .price {
            font-weight: 700;
            color: var(--primary);
        }

        /* STATUS BADGES */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .status-confirmed {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .status-refund {
            background: rgba(100, 116, 139, 0.1);
            color: var(--text-gray);
        }

        .date {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .btn-refund {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: white;
            color: var(--danger);
            border: 1px solid var(--danger);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .btn-refund:hover {
            background: var(--danger);
            color: white;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-gray);
        }

        @media (max-width: 1024px) {
            .table-wrapper {
                overflow-x: auto;
            }
            table {
                min-width: 900px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <i data-lucide="shopping-bag" style="width: 24px; color: var(--primary);"></i>
            Order<span>ly</span>
        </a>

        <div class="nav-links">
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="history.php" class="active">History</a>
        </div>

        <div class="nav-right">
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container">
        
        <h1 class="page-title"><i data-lucide="clock"></i> Transaction History</h1>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Info</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query) > 0) {
                        while($row = mysqli_fetch_assoc($query)){ 
                    ?>
                    <tr>
                        <td class="order-id">#<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <span class="product-name"><?= htmlspecialchars($row['product_name']); ?></span>
                            <div class="order-address"><i data-lucide="map-pin" style="width: 12px; display:inline-block; margin-right:4px;"></i> <?= htmlspecialchars($row['address']); ?></div>
                        </td>
                        <td><span class="method-badge"><?= htmlspecialchars($row['method']); ?></span></td>
                        
                        <td>
                        <?php
                            $status = $row['status'];
                            if($status == 'Pending'){
                                echo "<span class='status-badge status-pending'><i data-lucide='loader' style='width:14px;'></i> Pending</span>";
                            } elseif($status == 'Confirmed'){
                                echo "<span class='status-badge status-confirmed'><i data-lucide='check' style='width:14px;'></i> Confirmed</span>";
                            } elseif($status == 'Rejected'){
                                echo "<span class='status-badge status-rejected'><i data-lucide='x' style='width:14px;'></i> Rejected</span>";
                            } elseif($status == 'Refund Requested'){
                                echo "<span class='status-badge status-refund'>Refund Requested</span>";
                            } else {
                                echo "<span class='status-badge status-refund'>".$status."</span>";
                            }
                        ?>
                        </td>
                        
                        <td>
                            <?php if ($status == 'Confirmed' && !empty($row['delivery_status'])): 
                                $ds = $row['delivery_status'];
                                if ($ds == 'Packaging') {
                                    echo "<span style='color: var(--warning); font-weight:600; font-size: 0.85rem; display:flex; align-items:center; gap:4px;'><i data-lucide='box' style='width:14px;'></i> Packaging</span>";
                                } elseif ($ds == 'Dalam Perjalanan') {
                                    echo "<span style='color: var(--secondary); font-weight:600; font-size: 0.85rem; display:flex; align-items:center; gap:4px;'><i data-lucide='truck' style='width:14px;'></i> Dalam Perjalanan</span>";
                                } elseif ($ds == 'Selesai') {
                                    echo "<span style='color: var(--success); font-weight:600; font-size: 0.85rem; display:flex; align-items:center; gap:4px;'><i data-lucide='check-circle' style='width:14px;'></i> Selesai</span>";
                                }
                            ?>
                            <?php else: ?>
                                <span style="font-size: 0.85rem; color: var(--text-gray); font-weight: 500;">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="date"><?= date("d M Y", strtotime($row['order_date'])); ?></td>
                        <td class="price">Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                        <td>
                        <?php if($row['status'] == 'Confirmed'){ ?>
                            <a href="refund.php?order_id=<?= $row['id']; ?>" class="btn-refund">
                               <i data-lucide="refresh-ccw" style="width: 14px;"></i> Refund
                            </a>
                        <?php } else { ?>
                            <span style="color:var(--text-gray)">-</span>
                        <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='8'><div class='empty-state'><i data-lucide='inbox' style='width:40px; height:40px; margin-bottom:10px;'></i><br>No transaction history found.</div></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>