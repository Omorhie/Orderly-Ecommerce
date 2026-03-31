<?php
session_start();
require_once "../../config/database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// ambil gambar dulu
$query = $conn->query("SELECT image FROM products WHERE id=$id");
$data = $query->fetch_assoc();

if ($data && $data['image']) {
    unlink("../../uploads/products/" . $data['image']);
}

// delete produk
$conn->query("DELETE FROM products WHERE id=$id");

header("Location: index.php");
