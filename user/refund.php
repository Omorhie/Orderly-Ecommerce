<?php
session_start();

if(!isset($_SESSION['username_user'])){
    header("Location: ../auth/user/login.php");
    exit;
}

require_once "../config/database.php";
require_once "../config/notifications_helper.php";

if(!isset($_GET['order_id'])){
    header("Location: history.php");
    exit;
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// ambil data order
$query = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE id='$order_id' AND user_id='$user_id'
");

$order = mysqli_fetch_assoc($query);

if(!$order){
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>Order not found! <a href='history.php'>Back</a></div>";
    exit;
}

// proses submit refund
if(isset($_POST['submit'])){
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    mysqli_query($conn, "
        INSERT INTO refunds (order_id, reason, status) 
        VALUES ('$order_id', '$reason', 'pending')
    ");

    // update status order
    mysqli_query($conn, "
        UPDATE orders SET status='Refund Requested' 
        WHERE id='$order_id'
    ");

    /* ADD NOTIFICATION TO ADMIN */
    $refund_msg = "Permintaan refund baru masuk dari User ID: $user_id untuk Order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT);
    add_notification($conn, null, 'refund', $refund_msg);

    echo "<script>
        alert('Refund request submitted successfully!');
        window.location='history.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Refund - Orderly</title>
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
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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
            height: 100vh;
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

        /* MAIN CONTENT */
        .main-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .container h2 {
            font-size: 1.6rem;
            color: var(--primary);
            font-weight: 700;
        }

        .order-summary {
            background: var(--bg-color);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            border: 1px dashed #cbd5e1;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-weight: 700;
        }

        .label {
            color: var(--text-gray);
        }

        .value {
            color: var(--primary);
            font-weight: 600;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-size: 0.95rem;
            font-weight: 500;
        }

        textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-dark);
            background: var(--bg-color);
            resize: none;
            outline: none;
            transition: var(--transition);
        }

        textarea:focus {
            border-color: var(--danger);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        button {
            margin-top: 25px;
            padding: 16px;
            width: 100%;
            border: none;
            background: var(--danger);
            color: white;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        button:hover {
            background: #dc2626; /* Darker red */
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <i data-lucide="shopping-bag" style="width: 24px; color: var(--primary);"></i>
            Order<span>ly</span>
        </a>
        <div class="nav-right">
            <a href="history.php">
                <i data-lucide="x" style="width: 18px;"></i> Cancel
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="container">
            <div class="header">
                <div class="header-icon">
                    <i data-lucide="refresh-ccw" style="width: 30px; height: 30px;"></i>
                </div>
                <h2>Request Refund</h2>
            </div>

            <div class="order-summary">
                <div class="summary-row">
                    <span class="label">Product</span>
                    <span class="value"><?= htmlspecialchars($order['product_name']); ?></span>
                </div>
                <div class="summary-row">
                    <span class="label">Order ID</span>
                    <span class="value">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="summary-row">
                    <span class="label">Total Amount</span>
                    <span class="value">Rp <?= number_format($order['total_price'], 0, ',', '.'); ?></span>
                </div>
            </div>

            <form method="POST">
                <label>Reason for Refund</label>
                <textarea name="reason" placeholder="Please provide details about why you want to refund this item..." required></textarea>

                <button type="submit" name="submit">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>