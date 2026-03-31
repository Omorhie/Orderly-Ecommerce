<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// cek apakah sudah ada di cart
$check = mysqli_query($conn, "
    SELECT * FROM cart 
    WHERE user_id = $user_id AND product_id = $product_id
");

if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, "
        UPDATE cart 
        SET qty = qty + 1 
        WHERE user_id = $user_id AND product_id = $product_id
    ");
} else {
    mysqli_query($conn, "
        INSERT INTO cart (user_id, product_id, qty) 
        VALUES ($user_id, $product_id, 1)
    ");
}

// redirect ke cart
header("Location: cart.php");
exit;