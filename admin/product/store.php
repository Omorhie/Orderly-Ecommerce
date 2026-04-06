<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $price       = (int) $_POST['price'];
    $stock       = (int) $_POST['stock'];
    $size        = mysqli_real_escape_string($conn, $_POST['size']); // ✅ TAMBAHAN
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $imageName = "";

    // Upload image
    if (!empty($_FILES['image']['name'])) {

        $targetDir = "../../uploads/products/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

$imageName = time() . "_" . $_FILES["image"]["name"];

// bersihkan karakter berbahaya
$imageName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $imageName);
        $targetFile = $targetDir . $imageName;

        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // ✅ QUERY SUDAH DIPERBAIKI
$size = mysqli_real_escape_string($conn, $_POST['size']);

$query = "INSERT INTO products (name, description, price, stock, size, image)
VALUES ('$name', '$description', $price, $stock, '$size', '$imageName')";

if(!mysqli_query($conn, $query)){
    die("Error SQL: " . mysqli_error($conn));
}

    header("Location: index.php");
    exit;
}
?>