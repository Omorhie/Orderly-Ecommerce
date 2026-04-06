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
<html>
<head>
<title>Edit Profile</title>
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

/* FORM */
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

.form-group{
margin-bottom:15px;
}

.form-group label{
display:block;
margin-bottom:5px;
color:#4988C4;
}

.form-group input,
.form-group textarea{
width:100%;
padding:12px;
border-radius:10px;
border:none;
outline:none;
}

textarea{
resize:none;
height:100px;
}

/* BUTTON */
.btn{
margin-top:10px;
width:100%;
padding:14px;
background:#4988C4;
border:none;
border-radius:10px;
color:white;
font-size:16px;
cursor:pointer;
transition:.3s;
}

.btn:hover{
background:white;
color:#0F2854;
}

/* ERROR */
.error{
background:#ff4d4d;
padding:10px;
border-radius:10px;
margin-bottom:10px;
}
</style>
</head>

<body>

<div class="navbar">
    <div class="logo">Order<span>ly</span></div>
    <a href="profile.php">Back</a>
</div>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if(isset($error)){ ?>
        <div class="error"><?= $error; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address"><?= htmlspecialchars($user['address']); ?></textarea>
        </div>

        <button type="submit" class="btn">Save Changes</button>
    </form>
</div>

</body>
</html>