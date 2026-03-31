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
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $imageName = "";

    // Upload image
    if (!empty($_FILES['image']['name'])) {

        $targetDir = "../../uploads/products/";
        
        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // Insert ke database
    $query = "INSERT INTO products (name, description, price, stock, image)
              VALUES ('$name', '$description', '$price', '$stock', '$imageName')";

    mysqli_query($conn, $query);

    header("Location: index.php");
    exit;
}
?>
