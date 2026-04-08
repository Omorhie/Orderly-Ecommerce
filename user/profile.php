<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");

if(mysqli_num_rows($query) == 0){
    echo "User not found";
    exit;
}

$user = mysqli_fetch_assoc($query);

$order_query = mysqli_query($conn, "
    SELECT COUNT(DISTINCT order_id) as total_orders,
           SUM(price) as total_spent
    FROM transactions
    WHERE user_id = $user_id
");

$order_data = mysqli_fetch_assoc($order_query);

$alert_msg = "";
$alert_type = "";

if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Validasi password lama
    if(!password_verify($current, $user['password'])){
        $alert_msg = "Current password salah";
        $alert_type = "error";
    }
    // Validasi konfirmasi
    elseif($new !== $confirm){
        $alert_msg = "Konfirmasi password tidak sama";
        $alert_type = "error";
    }
    // Validasi panjang password
    elseif(strlen($new) < 6){
        $alert_msg = "Password minimal 6 karakter";
        $alert_type = "error";
    }
    else{
        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET password = '$new_hash' 
            WHERE id = $user_id
        ");

        $alert_msg = "Password berhasil diubah";
        $alert_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Orderly</title>
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
            --success: #22c55e;
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
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
            max-width: 900px;
            width: 95%;
            margin: 50px auto;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
        }

        /* SECTION BOX */
        .section-box {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 1.4rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(73, 136, 196, 0.08);
            border: 1px solid rgba(73, 136, 196, 0.2);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            background: rgba(73, 136, 196, 0.12);
        }

        .stat-icon {
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-gray);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* PROFILE ITEMS */
        .profile-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .profile-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .profile-label {
            font-size: 0.85rem;
            color: var(--text-gray);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-value {
            font-size: 1.05rem;
            color: var(--text-dark);
            font-weight: 600;
            background: var(--bg-color);
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .btn-edit {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            margin-top: 30px;
        }

        .btn-edit:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(73, 136, 196, 0.25);
        }

        /* FORM */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: var(--primary);
            background: var(--bg-color);
            transition: var(--transition);
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--secondary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(73, 136, 196, 0.1);
        }

        .btn-submit {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: var(--text-dark);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }

        /* ALERT */
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        @media (max-width: 800px) {
            .container {
                grid-template-columns: 1fr;
            }
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
        
        <!-- LEFT: DETAILS -->
        <div class="section-box">
            <h2 class="section-title"><i data-lucide="user"></i> Personal Information</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i data-lucide="shopping-cart"></i></div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?= $order_data['total_orders'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i data-lucide="wallet"></i></div>
                    <div class="stat-label">Total Spent</div>
                    <div class="stat-value">Rp <?= number_format($order_data['total_spent'] ?? 0) ?></div>
                </div>
            </div>

            <div class="profile-list">
                <div class="profile-item">
                    <div class="profile-label"><i data-lucide="at-sign" style="width: 14px;"></i> Username</div>
                    <div class="profile-value"><?= htmlspecialchars($user['username']); ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label"><i data-lucide="mail" style="width: 14px;"></i> Email Address</div>
                    <div class="profile-value"><?= htmlspecialchars($user['email']); ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label"><i data-lucide="phone" style="width: 14px;"></i> Phone Number</div>
                    <div class="profile-value"><?= htmlspecialchars($user['phone']); ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label"><i data-lucide="map-pin" style="width: 14px;"></i> Shipping Address</div>
                    <div class="profile-value"><?= $user['address'] ? htmlspecialchars($user['address']) : 'Not set'; ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label"><i data-lucide="calendar" style="width: 14px;"></i> Member Since</div>
                    <div class="profile-value"><?= date("d M Y", strtotime($user['created_at'])); ?></div>
                </div>
            </div>

            <a href="edit_profile.php" class="btn-edit">
                <i data-lucide="edit-3" style="width: 18px;"></i> Edit Profile Details
            </a>
        </div>

        <!-- RIGHT: PASSWORD -->
        <div class="section-box" style="height: fit-content; position: sticky; top: 100px;">
            <h2 class="section-title"><i data-lucide="lock"></i> Change Password</h2>

            <?php if($alert_msg != ""): ?>
                <div class="alert alert-<?= $alert_type; ?>">
                    <?php if($alert_type == 'error'): ?>
                        <i data-lucide="alert-circle" style="width: 18px;"></i>
                    <?php else: ?>
                        <i data-lucide="check-circle" style="width: 18px;"></i>
                    <?php endif; ?>
                    <?= $alert_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required placeholder="Enter new password (min. 6 chars)">
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password">
                </div>

                <button type="submit" name="change_password" class="btn-submit">
                    <i data-lucide="save" style="width: 18px;"></i> Save Password
                </button>
            </form>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>