<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    // kalau password diisi → update password
    if(!empty($password)){

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET username=?, email=?, no_hp=?, role=?, password=? 
            WHERE id=?
        ");

        $stmt->bind_param("sssssi", $username, $email, $no_hp, $role, $hashed, $id);

    } 
    // kalau password kosong → tidak update password
    else{

        $stmt = $conn->prepare("
            UPDATE officer 
            SET username=?, email=?, no_hp=?, role=? 
            WHERE id=?
        ");

        $stmt->bind_param("ssssi", $username, $email, $no_hp, $role, $id);
    }

    // jalankan query
    if ($stmt->execute()) {
        header("Location: index.php?success=updated");
        exit;
    } else {
        echo "Gagal update user";
    }

    $stmt->close();
    $conn->close();
}
?>
