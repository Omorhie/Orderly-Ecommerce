<?php
session_start();

if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

require_once "../../config/database.php";

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        header("Location: index.php?success=stock_deleted");
        exit;
    }else{
        echo "Gagal menghapus data stock";
    }

    $stmt->close();
    $conn->close();
}
?>