<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id']);
$action = $_GET['action'];

// pastikan cart milik user
$check = mysqli_query($conn,
    "SELECT * FROM cart WHERE id = $id AND user_id = $user_id");

if(mysqli_num_rows($check) == 0){
    header("Location: cart.php");
    exit;
}

if($action == "increase"){
    mysqli_query($conn,
        "UPDATE cart SET qty = qty + 1 WHERE id = $id");
}

if($action == "decrease"){
    mysqli_query($conn,
        "UPDATE cart SET qty = qty - 1 WHERE id = $id AND qty > 1");
}

header("Location: cart.php");
exit;