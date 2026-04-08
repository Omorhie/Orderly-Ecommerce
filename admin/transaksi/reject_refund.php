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
    UPDATE refunds SET status='rejected' WHERE id='$id'
");

// kembalikan status order ke confirmed
mysqli_query($conn, "
    UPDATE orders SET status='Confirmed' WHERE id='$order_id'
");

header("Location: refund-control.php");