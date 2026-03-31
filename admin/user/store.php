<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // validasi basic
    if(empty($username) || empty($email) || empty($password) || empty($role)){
        die("Data tidak lengkap");
    }

    // hash password (WAJIB)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // cek username / email sudah ada
    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        echo "<script>alert('Username atau email sudah digunakan');history.back();</script>";
        exit;
    }

    // insert user
    $stmt = $conn->prepare("
        INSERT INTO users (username,email,no_hp,password,role)
        VALUES (?,?,?,?,?)
    ");

    $stmt->bind_param(
        "sssss",
        $username,
        $email,
        $no_hp,
        $hashedPassword,
        $role
    );

    if($stmt->execute()){
        header("Location: index.php");
    } else {
        echo "Gagal menambah user";
    }
}
