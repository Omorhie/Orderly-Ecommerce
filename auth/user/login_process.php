<?php
session_start();
include "../../config/database.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");
$user = mysqli_fetch_assoc($query);

if($user && password_verify($password,$user['password'])){
$_SESSION['user_id'] = $user['id'];
$_SESSION['username_user'] = $user['username'];

    header("Location: ../../user/dashboard.php");
}else{
    echo "Username atau password salah";
}