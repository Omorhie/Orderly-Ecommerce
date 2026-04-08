<?php
session_start();

if (!isset($_SESSION['officer_role']) || $_SESSION['officer_role'] != 'petugas') {
    die("Akses ditolak");
}

require_once "../../config/database.php";

if(isset($_GET['id'])){

    $order_id = (int) $_GET['id'];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET status='Rejected' 
        WHERE id=?
    ");

    $stmt->bind_param("i", $order_id);

    if($stmt->execute()){
        echo "<script>
            alert('Transaksi ditolak');
            window.location='index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menolak transaksi');
            window.location='index.php';
        </script>";
    }
}
?>