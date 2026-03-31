<?php
session_start();

if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

require_once "../../config/database.php";

if(!isset($_GET['id'])){
    die("ID tidak ditemukan");
}

$id = intval($_GET['id']);

/*
Pastikan tabel transactions punya:
- user_id
- product_name
- price
- qty
- created_at
*/

$stmt = $conn->prepare("
    SELECT 
        transactions.*,
        users.username
    FROM transactions
    JOIN users ON transactions.user_id = users.id
    WHERE transactions.id = ?
");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(!$data){
    die("Data tidak ditemukan");
}

$total = $data['price'] * ($data['qty'] ?? 1);
?>

<!DOCTYPE html>
<html>
<head>
<title>Receipt</title>

<style>
body{
    font-family:Arial;
    background:#27374D;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    overflow: hidden;
}

.receipt{
    background:#D9D9D9;
    padding:30px;
    border-radius:16px;
    width:420px;
    text-align:center;
}

h2{
    margin-bottom:20px;
}

.row{
    display:flex;
    justify-content:space-between;
    margin:10px 0;
}

.total{
    font-size:20px;
    font-weight:bold;
    margin-top:20px;
}

button{
    margin-top:20px;
    padding:10px 20px;
    border:none;
    background:#526D82;
    color:white;
    border-radius:8px;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="receipt">

<h2>Payment Receipt</h2>

<div class="row">
<span>Customer</span>
<span><?= htmlspecialchars($data['username']) ?></span>
</div>

<div class="row">
<span>Product</span>
<span><?= htmlspecialchars($data['product_name']) ?></span>
</div>

<div class="row">
<span>Qty</span>
<span><?= $data['qty'] ?? 1 ?></span>
</div>

<div class="row">
<span>Price</span>
<span>Rp <?= number_format($data['price']) ?></span>
</div>

<div class="row">
<span>Date</span>
<span><?= $data['created_at'] ?></span>
</div>

<div class="total">
Total: Rp <?= number_format($total) ?>
</div>

<button onclick="window.print()">Print</button>

</div>

</body>
</html>