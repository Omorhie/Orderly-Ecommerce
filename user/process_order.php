<?php
session_start();
require_once "../config/database.php";
require_once "../config/notifications_helper.php";

if(!isset($_SESSION['user_id'])){
    exit("Harus login sebagai user");
}

$user_id = $_SESSION['user_id'];
$method  = $_POST['method'];

$courier = isset($_POST['courier']) ? $_POST['courier'] : 'Unknown Courier';
$shipping_service = isset($_POST['shipping_service']) ? $_POST['shipping_service'] : 'Regular';

// Hitung ongkir
$shipping_cost = ($shipping_service === 'Express') ? 35000 : 15000;

// Sisipkan info pengiriman ke dalam string address supaya UI admin & history tidak rusak
$raw_address = $_POST['address'];
$full_address = $raw_address . " (Shipping: $courier - $shipping_service [Rp " . number_format($shipping_cost, 0, ',', '.') . "])";
$address = mysqli_real_escape_string($conn, $full_address);

$total_price = $shipping_cost; // Basis harganya dimulai dari ongkos kirim
$product_names = [];

/* =========================
   AMBIL CART USER
========================= */
$cart = mysqli_query($conn, "
    SELECT cart.*, products.name, products.price
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = $user_id
");

/* =========================
   HITUNG TOTAL
========================= */
while($row = mysqli_fetch_assoc($cart)){
    $total_price += $row['price'] * $row['qty'];
    $product_names[] = $row['name'];
}

if($total_price == 0){
    exit("Cart kosong");
}

$product_name_string = mysqli_real_escape_string(
    $conn,
    implode(", ", $product_names)
);
/* =========================
   INSERT KE ORDERS
========================= */
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
mysqli_query($conn, "
    INSERT INTO orders 
    (user_id, product_name, phone, address, method, total_price, status)
    VALUES
    ('$user_id','$product_name_string','$phone','$address','$method','$total_price','Pending')
");

$order_id = mysqli_insert_id($conn);

/* =========================
   UPLOAD BUKTI TRANSFER
========================= */
$proofPath = "";

if($method == "Transfer" && !empty($_FILES['proof_payment']['name'])){

    $targetDir = "../uploads/proofs/";
    if(!is_dir($targetDir)){
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["proof_payment"]["name"]);
    $targetFile = $targetDir . $fileName;

    move_uploaded_file($_FILES["proof_payment"]["tmp_name"], $targetFile);

    $proofPath = "uploads/proofs/" . $fileName;
}

/* =========================
   AMBIL ULANG CART
========================= */
$cart = mysqli_query($conn, "
    SELECT cart.*, products.name, products.price
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = $user_id
");

/* =========================
   INSERT KE TRANSACTIONS
========================= */
while($row = mysqli_fetch_assoc($cart)){

    $product_name = $row['name'];
    $qty   = $row['qty'];
    $price = $row['price'] * $qty;

$stmt = $conn->prepare("
    INSERT INTO transactions
    (order_id, user_id, product_name, qty, price, proof_payment)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("iisiss",
    $order_id,
    $user_id,
    $product_name,
    $qty,
    $price,
    $proofPath
);

$stmt->execute();

    // Kurangi stok
    mysqli_query($conn, "
        UPDATE products
        SET stock = stock - $qty
        WHERE id = ".$row['product_id']."
    ");
}

/* =========================
   HAPUS CART
========================= */
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

/* =========================
   ADD NOTIFICATION
========================= */
$order_msg = "Pesanan baru telah masuk! Order ID: #" . str_pad($order_id, 5, '0', STR_PAD_LEFT);
add_notification($conn, null, 'order', $order_msg);

/* =========================
   REDIRECT
========================= */
header("Location: history.php");
exit;