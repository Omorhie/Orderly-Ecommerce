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

if(isset($_POST['change_password'])){

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Validasi password lama
    if(!password_verify($current, $user['password'])){
        echo "<script>alert('Current password salah');</script>";
    }
    // Validasi konfirmasi
    elseif($new !== $confirm){
        echo "<script>alert('Konfirmasi password tidak sama');</script>";
    }
    // Validasi panjang password
    elseif(strlen($new) < 6){
        echo "<script>alert('Password minimal 6 karakter');</script>";
    }
    else{
        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET password = '$new_hash' 
            WHERE id = $user_id
        ");

        echo "<script>alert('Password berhasil diubah'); window.location='profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, sans-serif;
}


/* NAVBAR */
.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px 40px;
background:#0F2854;
color:white;
}

.logo{
font-size:24px;
}

.logo span{
color:#4988C4;
}

.navbar a{
color:white;
text-decoration:none;
transition: all 300ms;
}

.navbar a:hover{
    color: #4988C4 ;
}

/* PROFILE CARD */
.container{
max-width:600px;
margin:80px auto;
background:#0F2854;
padding:40px;
border-radius:20px;
color:white;
box-shadow:0 20px 40px rgba(0,0,0,0.2);
}

.container h2{
margin-bottom:20px;
}

.profile-item{
margin-bottom:15px;
}

.label{
color:#4988C4;
font-size:14px;
}

.value{
font-size:16px;
font-weight:500;
}

.btn{
margin-top:20px;
display:inline-block;
padding:12px 20px;
background:#4988C4;
color:white;
border-radius:10px;
text-decoration:none;
transition:.3s;
border-color: transparent;
}

.btn:hover{
background:white;
color:#0F2854;
}
</style>
</head>

<body>

<div class="navbar">
    <div class="logo">Order<span>ly</span></div>
    <a href="dashboard.php">Back</a>
</div>

<div class="container">
    <h2>My Profile</h2>

    <div style="display:flex; justify-content:space-between; margin-bottom:25px; gap:15px;">
    
    <div style="flex:1; background:#4988C4; padding:15px; border-radius:10px; text-align:center;">
        <div style="font-size:14px;">Total Orders</div>
        <div style="font-size:20px; font-weight:bold;">
            <?= $order_data['total_orders'] ?? 0 ?>
        </div>
    </div>

    <div style="flex:1; background:#4988C4; padding:15px; border-radius:10px; text-align:center;">
        <div style="font-size:14px;">Total Spent</div>
        <div style="font-size:20px; font-weight:bold;">
            Rp <?= number_format($order_data['total_spent'] ?? 0) ?>
        </div>
    </div>

</div>

    <div class="profile-item">
        <div class="label">Username</div>
        <div class="value"><?= htmlspecialchars($user['username']); ?></div>
    </div>

    <div class="profile-item">
        <div class="label">Email</div>
        <div class="value"><?= htmlspecialchars($user['email']); ?></div>
    </div>

    <div class="profile-item">
        <div class="label">Phone</div>
        <div class="value"><?= htmlspecialchars($user['phone']); ?></div>
    </div>

    <div class="profile-item">
        <div class="label">Address</div>
        <div class="value">
            <?= $user['address'] ? htmlspecialchars($user['address']) : 'Not set'; ?>
        </div>
    </div>

    <div class="profile-item">
        <div class="label">Joined</div>
        <div class="value"><?= $user['created_at']; ?></div>
    </div>

    <a href="edit_profile.php" class="btn">Edit Profile</a>
    <h3 style="margin-top:30px;">Change Password</h3>

<form method="POST" style="margin-top:15px;">
    
    <div class="profile-item">
        <div class="label">Current Password</div>
        <input type="password" name="current_password" required 
        style="width:100%; padding:10px; border-radius:8px; border:none;">
    </div>

    <div class="profile-item">
        <div class="label">New Password</div>
        <input type="password" name="new_password" required 
        style="width:100%; padding:10px; border-radius:8px; border:none;">
    </div>

    <div class="profile-item">
        <div class="label">Confirm New Password</div>
        <input type="password" name="confirm_password" required 
        style="width:100%; padding:10px; border-radius:8px; border:none;">
    </div>

    <button type="submit" name="change_password" class="btn">
        Update Password
    </button>
</form>
</div>

</body>
</html>