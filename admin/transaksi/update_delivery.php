<?php
session_start();

if (!isset($_SESSION['officer_role']) || $_SESSION['officer_role'] != 'petugas') {
    die("Akses ditolak");
}

require_once "../../config/database.php";
require_once "../../config/notifications_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['delivery_status'])) {
    
    $order_id = intval($_POST['order_id']);
    $status = $_POST['delivery_status'];
    
    $allowed_statuses = ['Packaging', 'Dalam Perjalanan', 'Selesai'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid delivery status.");
    }
    
    $stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $q = $conn->query("SELECT user_id FROM orders WHERE id=" . intval($order_id));
        if ($q && $q->num_rows > 0) {
            $u = $q->fetch_assoc();
            add_notification($conn, $u['user_id'], 'delivery', "Status pengiriman Order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " diperbarui menjadi: " . $status);
        }
        header("Location: index.php?delivery_update=success");
        exit;
    } else {
        echo "Gagal mengupdate status kurir.";
    }
} else {
    header("Location: index.php");
    exit;
}
