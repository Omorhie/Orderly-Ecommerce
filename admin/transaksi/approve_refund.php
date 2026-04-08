<?php
require_once "../../config/database.php";

$id = $_GET['id'];

// ambil order_id
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM refunds WHERE id='$id'
"));

$order_id = $data['order_id'];

// update refund
mysqli_query($conn, "
    UPDATE refunds SET status='approved' WHERE id='$id'
");

// update order
mysqli_query($conn, "
    UPDATE orders SET status='Refunded' WHERE id='$order_id'
");

header("Location: refund-control.php");