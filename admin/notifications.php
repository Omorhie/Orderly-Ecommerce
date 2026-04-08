<?php
session_start();

if (!isset($_SESSION['officer_role'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

$role = $_SESSION['officer_role'];

// Mark all as read when opening page
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id IS NULL");

// Fetch
$q = $conn->query("SELECT * FROM notifications WHERE user_id IS NULL ORDER BY created_at DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Orderly Admin</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        /* SIDEBAR (standard) */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }
        .sidebar h2 { text-align: center; font-size: 26px; font-weight: 700; color: var(--primary); margin-bottom: 40px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .sidebar h2 span { color: var(--accent); }
        .sidebar ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .sidebar ul li a { text-decoration: none; color: var(--text-gray); display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; font-size: 15px; font-weight: 500; transition: var(--transition); }
        .sidebar ul li a i { width: 20px; height: 20px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: var(--primary); color: #ffffff; transform: translateX(5px); box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2); }
        .sidebar ul li:last-child { margin-top: auto; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .sidebar ul li:last-child a:hover { background-color: #ef4444; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }

        /* CONTENT */
        .content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            background: var(--sidebar-bg);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notif-container {
            background: var(--sidebar-bg);
            border-radius: 20px;
            padding: 10px;
            box-shadow: var(--card-shadow);
        }

        .notif-item {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-order { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .icon-refund { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .icon-chat { background: rgba(16, 185, 129, 0.1); color: #10b981; }

        .notif-content {
            flex: 1;
        }

        .notif-message {
            font-size: 15px;
            color: var(--text-dark);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .notif-time {
            font-size: 12px;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-gray);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
        <ul>
            <li><a href="dashboard.php"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
            <li><a href="product/index.php"><i data-lucide="package"></i> Products</a></li>
            <li><a href="transaksi/index.php"><i data-lucide="shopping-cart"></i> Transactions</a></li>
            <li><a href="laporan/index.php"><i data-lucide="file-bar-chart"></i> Reports</a></li>
            <?php if($role === 'admin') { ?>
                <li><a href="user/index.php"><i data-lucide="users"></i> Officers</a></li>
                <li><a href="backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
            <?php } ?>
            <li><a href="notifications.php" class="active"><i data-lucide="bell"></i> Notifications</a></li>
            <li><a href="chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
            <li><a href="../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h2><i data-lucide="bell" style="width:24px; color:var(--accent);"></i> System Notifications</h2>
        </div>

        <div class="notif-container">
            <?php if($q->num_rows > 0){
                while($n = $q->fetch_assoc()){ 
                    $iconClass = "icon-order";
                    $iconName = "info";
                    if(strpos(strtolower($n['type']), 'order') !== false) { $iconClass = "icon-order"; $iconName = "shopping-bag"; }
                    if(strpos(strtolower($n['type']), 'refund') !== false) { $iconClass = "icon-refund"; $iconName = "refresh-ccw"; }
                    if(strpos(strtolower($n['type']), 'chat') !== false) { $iconClass = "icon-chat"; $iconName = "message-circle"; }
            ?>
            <div class="notif-item">
                <div class="notif-icon <?= $iconClass ?>">
                    <i data-lucide="<?= $iconName ?>"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-message"><?= htmlspecialchars($n['message']) ?></div>
                    <div class="notif-time">
                        <i data-lucide="clock" style="width:12px;"></i> <?= date('d M Y - H:i', strtotime($n['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php } } else { ?>
                <div class="empty-state">
                    <i data-lucide="bell-off" style="width:50px; height:50px; margin-bottom:15px; opacity:0.5;"></i>
                    <p>You're all caught up! No notifications right now.</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
