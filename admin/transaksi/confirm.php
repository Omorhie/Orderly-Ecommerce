<?php
session_start();

if (!isset($_SESSION['officer_role']) || $_SESSION['officer_role'] != 'petugas') {
    die("Akses ditolak");
}

require_once "../../config/database.php";
require_once "../../config/notifications_helper.php";

if(isset($_GET['id'])){

    $order_id = $_GET['id'];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET status='Confirmed', delivery_status='Packaging'
        WHERE id=?
    ");

    $stmt->bind_param("i", $order_id);

    if($stmt->execute()){
        $q = $conn->query("SELECT user_id FROM orders WHERE id=" . intval($order_id));
        if ($q && $q->num_rows > 0) {
            $u = $q->fetch_assoc();
            add_notification($conn, $u['user_id'], 'delivery', "Pesanan #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " telah dikonfirmasi dan sedang dikemas.");
        }
        header("Location: index.php?success=confirmed");
        exit;
    } else {
        echo "Gagal konfirmasi";
    }
}