<?php
session_start();
require_once "../../config/database.php";

$login    = $_POST['login'];
$password = $_POST['password'];

$query = "SELECT * FROM officer WHERE username = ? OR email = ?";
$stmt  = $conn->prepare($query);
$stmt->bind_param("ss", $login, $login);
$stmt->execute();

$result = $stmt->get_result();
$user   = $result->fetch_assoc();

if ($user) {
    if (password_verify($password, $user['password'])) {

        // simpan session
        $_SESSION['officer_id'] = $user['id'];
        $_SESSION['officer_username'] = $user['username'];
        $_SESSION['officer_role'] = $user['role'];

        // redirect sesuai role
        if ($user['role'] == 'admin') {
            header("Location: ../../admin/dashboard.php");
        } elseif ($user['role'] == 'petugas') {
            header("Location: ../../admin/dashboard.php");
        } else {
            header("Location: ../user/home.php");
        }
        exit;

    } else {
        echo "Password salah";
    }
} else {
    echo "Akun tidak ditemukan";
}
