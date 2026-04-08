<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

// ambil data
$stock = $conn->query("SELECT id, name, stock, price, created_at FROM products ORDER BY id DESC");
$sales = $conn->query("
    SELECT 
        transactions.id,
        transactions.order_id,
        transactions.product_name,
        transactions.qty,
        transactions.created_at,
        orders.status AS order_status
    FROM transactions
    JOIN orders ON transactions.order_id = orders.id
    WHERE orders.status = 'confirmed'
    ORDER BY transactions.order_id DESC
");
$transaction = $conn->query("
    SELECT 
        transactions.*, 
        users.username,
        orders.method,
        orders.address,
        orders.status AS order_status
    FROM transactions
    JOIN users ON transactions.user_id = users.id
    JOIN orders ON transactions.order_id = orders.id
    ORDER BY transactions.id DESC
");

$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Orderly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            /* Admin Slate Palette */
            --primary: #1e293b;
            --secondary: #334155;
            --accent: #3b82f6;
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }

        /* SIDEBAR */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); padding: 30px 20px; box-shadow: 2px 0 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; z-index: 10; }
        .sidebar h2 { text-align: center; font-size: 26px; font-weight: 700; color: var(--primary); margin-bottom: 40px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .sidebar h2 span { color: var(--accent); }
        .sidebar ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .sidebar ul li a { text-decoration: none; color: var(--text-gray); display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; font-size: 15px; font-weight: 500; transition: var(--transition); }
        .sidebar ul li a i { width: 20px; height: 20px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: var(--primary); color: #ffffff; transform: translateX(5px); box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2); }
        .sidebar ul li:last-child { margin-top: auto; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .sidebar ul li:last-child a:hover { background-color: #ef4444; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }

        /* CONTENT */
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px; }
        
        .header-top { display: flex; justify-content: space-between; align-items: center; background: var(--sidebar-bg); padding: 20px 30px; border-radius: 20px; box-shadow: var(--card-shadow); }
        .header-top h2 { font-size: 22px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        
        /* MODERN SWITCH TABS */
        .switch-container {
            display: inline-flex;
            background: #e2e8f0;
            padding: 6px;
            border-radius: 16px;
            gap: 6px;
        }

        .switch-btn {
            background: transparent;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            color: var(--text-gray);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .switch-btn:hover {
            color: var(--primary);
        }

        .switch-btn.active {
            background: white;
            color: var(--accent);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* TABLE SECTIONS */
        .table-section {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        .table-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--card-shadow); }
        th, td { padding: 16px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: rgba(248, 250, 252, 0.8); color: var(--text-gray); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { color: var(--text-dark); font-size: 14.5px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        /* BADGES */
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
        .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .badge-confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .badge-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        /* Action Buttons */
        .action-group { display: flex; align-items: center; gap: 8px; }
        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none; transition: var(--transition); }
        .btn-action i { width: 18px; height: 18px; }
        .btn-action.delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-action.delete:hover { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }
        .btn-action.view { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .btn-action.view:hover { background: var(--accent); color: white; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }

        .hash-id { font-family: monospace; color: var(--text-gray); font-weight: 600; }

    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
    <ul>
        <li><a href="../dashboard.php"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
        <li><a href="../product/index.php"><i data-lucide="package"></i> Products</a></li>
        <li><a href="../transaksi/index.php"><i data-lucide="shopping-cart"></i> Transactions</a></li>
        <li><a href="index.php" class="active"><i data-lucide="file-bar-chart"></i> Reports</a></li>
        <?php if($role === 'admin') { ?>
            <li><a href="../user/index.php"><i data-lucide="users"></i> Officers</a></li>
            <li><a href="../backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
        <?php } ?>
        <li><a href="../notifications.php"><i data-lucide="bell"></i> Notifications <span class="notif-badge" style="background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;">0</span></a></li>
            <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
        <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="header">
        <div class="header-top">
            <h2><i data-lucide="file-bar-chart"></i> Report Central</h2>
        </div>

        <div class="switch-container">
            <button class="switch-btn active" onclick="showTable('stock', this)"><i data-lucide="package"></i> Stock Log</button>
            <button class="switch-btn" onclick="showTable('sales', this)"><i data-lucide="trending-up"></i> Confirmed Sales</button>
            <button class="switch-btn" onclick="showTable('transaction', this)"><i data-lucide="file-text"></i> Full Transactions</button>
        </div>
    </div>

    <!-- ================= STOCK ================= -->
    <div id="stock" class="table-section active">
        <table>
            <tr>
                <th>Item ID</th>
                <th>Product Name</th>
                <th>Remaining Stock</th>
                <th>Price Unit</th>
                <th>Creation Date</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $stock->fetch_assoc()){ ?>
            <tr>
                <td><span class="hash-id">#ITM-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                <td style="font-weight: 500; color: var(--primary);"><?= htmlspecialchars($row['name']) ?></td>
                <td>
                    <?php if($row['stock'] > 10): ?>
                        <span style="color:#10b981; font-weight:600;"><i data-lucide="box" style="width:14px; margin-bottom:-2px;"></i> <?= $row['stock'] ?></span>
                    <?php else: ?>
                        <span style="color:#f59e0b; font-weight:600;"><i data-lucide="alert-circle" style="width:14px; margin-bottom:-2px;"></i> <?= $row['stock'] ?></span>
                    <?php endif; ?>
                </td>
                <td>Rp <?= number_format($row['price']) ?></td>
                <td style="color: var(--text-gray); font-size: 13px;"><?= date('d M Y, H:i', strtotime($row['created_at'] ?? 'now')) ?></td>
                <td>
                    <div class="action-group">
                        <a href="delete_stock.php?id=<?= $row['id'] ?>" onclick="return confirm('Ensure this is necessary. Proceed?')" class="btn-action delete" title="Remove Log">
                            <i data-lucide="trash-2"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- ================= SALES ================= -->
    <div id="sales" class="table-section">
        <table>
            <tr>
                <th>Order Ref</th>
                <th>Target Product</th>
                <th>Qty Sold</th>
                <th>Checkout Sequence</th>
                <th>Final Status</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $sales->fetch_assoc()){ ?>
            <tr>
                <td><span class="hash-id">#ORD-<?= str_pad($row['order_id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                <td style="font-weight: 500;"><?= htmlspecialchars($row['product_name']) ?></td>
                <td style="font-weight: 600; color: var(--accent);">x<?= $row['qty'] ?></td>
                <td style="color: var(--text-gray); font-size: 13px;"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php
                    $status = strtolower($row['order_status']);
                    if($status == 'confirmed') echo '<span class="badge badge-confirmed"><i data-lucide="check-circle" style="width:12px;"></i> Confirmed</span>';
                    elseif($status == 'rejected') echo '<span class="badge badge-rejected"><i data-lucide="x-circle" style="width:12px;"></i> Rejected</span>';
                    else echo '<span class="badge badge-pending">Pending</span>';
                    ?>
                </td>
                <td>
                    <div class="action-group">
                        <a href="delete_sales.php?id=<?= $row['id'] ?>" onclick="return confirm('Remove this sales record?')" class="btn-action delete" title="Remove Record">
                            <i data-lucide="trash-2"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- ================= TRANSACTION ================= -->
    <div id="transaction" class="table-section">
        <table>
            <tr>
                <th>Record ID</th>
                <th>Client Name</th>
                <th>Target Product</th>
                <th>Shipping Details</th>
                <th>Monetary Value</th>
                <th>Date Logged</th>
                <th>Status Audit</th>
                <th>Report File</th>
            </tr>
            <?php while($row = $transaction->fetch_assoc()){ ?>
            <tr>
                <td><span class="hash-id">#TXN-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                <td style="font-weight: 500; color: var(--primary);"><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td>
                    <?php
                    if (preg_match('/\(Shipping: (.*?) - (.*?) \[(.*?)\]\)/', $row['address'], $matches)) {
                        echo "<div style='font-size:13px; color:var(--primary); font-weight:600;'>".$matches[1]." <span style='font-weight:500;'>(".$matches[2].")</span></div>";
                        echo "<div style='font-size:12px; color:var(--text-gray);'>".$matches[3]."</div>";
                    } else {
                        echo "<span style='color:#94a3b8; font-size:12px;'>Standard</span>";
                    }
                    ?>
                </td>
                <td style="font-weight: 600;">Rp <?= number_format($row['price']) ?></td>
                <td style="color: var(--text-gray); font-size: 13px;"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php
                    $status = strtolower($row['order_status']);
                    if($status == 'confirmed') echo '<span class="badge badge-confirmed"><i data-lucide="check-circle" style="width:12px;"></i> Confirmed</span>';
                    elseif($status == 'rejected') echo '<span class="badge badge-rejected"><i data-lucide="x-circle" style="width:12px;"></i> Rejected</span>';
                    else echo '<span class="badge badge-pending">Pending</span>';
                    ?>
                </td>
                <td>
                    <div class="action-group">
                        <a href="receipt.php?id=<?= $row['id'] ?>" class="btn-action view" title="Generate Receipt" target="_blank">
                            <i data-lucide="printer"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

<script>
    lucide.createIcons();

    function showTable(id, btn){
        // Hide all tables
        document.querySelectorAll(".table-section").forEach(t => t.classList.remove("active"));
        // Remove active class from buttons
        document.querySelectorAll(".switch-btn").forEach(b => b.classList.remove("active"));
        // Show target table and activate button
        document.getElementById(id).classList.add("active");
        btn.classList.add("active");
    }
</script>


    <!-- NOTIF LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../api_notif.php')
            .then(r => r.json())
            .then(data => {
                let badge = document.querySelector('.notif-badge');
                if(badge && data.count > 0) {
                    badge.style.display = 'inline-block';
                    badge.textContent = data.count;
                }
            }).catch(e=>console.log(e));
        });
    </script>
</body>
</html>
