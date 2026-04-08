<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);

// PROSES UPDATE
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);

    $update = "UPDATE users SET 
                username='$username',
                email='$email',
                phone='$phone',
                address='$address'
               WHERE id=$user_id";

    if(mysqli_query($conn, $update)){
        header("Location: profile.php");
        exit;
    } else {
        $error = "Failed to update profile";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Orderly</title>
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
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
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

        /* FORM CONTAINER */
        .container {
            max-width: 600px;
            width: 95%;
            margin: 60px auto;
            background: var(--white);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .container h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }

        /* FORM */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-gray);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            color: var(--primary);
            background: var(--bg-color);
            transition: var(--transition);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--secondary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(73, 136, 196, 0.1);
        }

        textarea.form-control {
            resize: none;
            height: 120px;
            font-family: inherit;
        }

        /* BUTTON */
        .btn-save {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 16px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(15, 40, 84, 0.15);
        }

        .btn-save:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(73, 136, 196, 0.25);
        }

        /* ERROR */
        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
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
            <a href="profile.php">
                <i data-lucide="x" style="width: 18px;"></i> Cancel
            </a>
        </div>
    </nav>

    <div class="container">
        <h2>Edit Profile</h2>

        <?php if(isset($error)){ ?>
            <div class="error-alert">
                <i data-lucide="alert-triangle"></i> <?= $error; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']); ?></textarea>
            </div>

            <button type="submit" class="btn-save">
                <i data-lucide="save"></i> Save Changes
            </button>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>