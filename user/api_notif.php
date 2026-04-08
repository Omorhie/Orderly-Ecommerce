<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

require_once "../config/database.php";
require_once "../config/notifications_helper.php";

echo json_encode(['count' => get_unread_count($conn, $_SESSION['user_id'])]);
?>
