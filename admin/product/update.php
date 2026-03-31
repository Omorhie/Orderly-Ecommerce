<?php
session_start();
require_once "../../config/database.php";
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = intval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $imageQuery = "";

    if (!empty($_FILES['image']['name'])) {

        $targetDir = "../../uploads/products/";

        if(!is_dir($targetDir)){
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)){
            $imageQuery = ", image='$imageName'";
        }
    }

    $query = "UPDATE products SET
              name='$name',
              price='$price',
              stock='$stock',
              description='$description'
              $imageQuery
              WHERE id='$id'";

    if(!mysqli_query($conn, $query)){
        die("Update gagal: " . mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}