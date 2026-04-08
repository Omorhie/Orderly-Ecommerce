<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/user/login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

// Mark all as read when opening page
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");

// Fetch
$q = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Orderly</title>
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
        .logo span { color: var(--secondary); }

        .nav-right a {
            padding: 10px 24px;
            background: var(--bg-color);
            color: var(--primary);
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
            color: var(--white);
        }

        .container {
            max-width: 800px;
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

        .notif-wrapper {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .notif-item {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: var(--transition);
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item:hover {
            background: #f8fafc;
        }

        .notif-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
        }

        .icon-delivery { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .icon-chat { background: var(--bg-color); color: var(--text-dark); }
        .icon-system { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        .notif-content {
            flex: 1;
        }

        .notif-message {
            font-size: 1rem;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .notif-time {
            font-size: 0.85rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .empty-state {
            padding: 80px 20px;
            text-align: center;
            color: var(--text-gray);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
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
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back to Shop
            </a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title"><i data-lucide="bell" style="color:var(--secondary);"></i> Notifications</h1>

        <div class="notif-wrapper">
            <?php if($q->num_rows > 0): ?>
                <?php while($n = $q->fetch_assoc()): 
                    $type = strtolower($n['type']);
                    $iconClass = "icon-system";
                    $iconName = "info";
                    
                    if(strpos($type, 'delivery') !== false || strpos($type, 'order') !== false) {
                        $iconClass = "icon-delivery";
                        $iconName = "package";
                    } else if(strpos($type, 'chat') !== false) {
                        $iconClass = "icon-chat";
                        $iconName = "message-circle";
                    }
                ?>
                <div class="notif-item">
                    <div class="notif-icon <?= $iconClass ?>">
                        <i data-lucide="<?= $iconName ?>"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-message"><?= nl2br(htmlspecialchars($n['message'])) ?></div>
                        <div class="notif-time">
                            <i data-lucide="clock" style="width:14px;"></i> <?= date('d M Y - H:i', strtotime($n['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="bell-off" style="width:60px; height:60px; opacity:0.5; color:var(--text-gray);"></i>
                    <h3>No Notifications Yet</h3>
                    <p>When you have new messages or updates about your orders,<br>they will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
