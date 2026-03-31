<?php
session_start();
include '../config/database.php';

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

mysqli_query($conn, 
    "DELETE FROM cart 
     WHERE id = $id 
     AND user_id = $user_id");

header("Location: cart.php");
exit;