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

body{
background:linear-gradient(135deg,#eef2f7,#d9e2ec);
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
</div>

</body>
</html>