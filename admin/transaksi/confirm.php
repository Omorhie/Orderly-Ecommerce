<?php
session_start();

if (!isset($_SESSION['officer_role']) || $_SESSION['officer_role'] != 'petugas') {
    die("Akses ditolak");
}

require_once "../../config/database.php";

if(isset($_GET['id'])){

    $order_id = $_GET['id'];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET status='Confirmed' 
        WHERE id=?
    ");

    $stmt->bind_param("i", $order_id);

    if($stmt->execute()){
        header("Location: index.php?success=confirmed");
        exit;
    } else {
        echo "Gagal konfirmasi";
    }
}