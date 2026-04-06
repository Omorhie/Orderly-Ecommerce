<?php
include "../../config/database.php";

$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// CEK USERNAME
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

if(mysqli_num_rows($cek) > 0){
    echo "Username sudah digunakan!";
    exit;
}

// INSERT DATA
$query = mysqli_query($conn,"INSERT INTO users(username,email,phone,password)
VALUES('$username','$email','$phone','$password')");

if($query){
    echo "Register berhasil";
}else{
    echo "Register gagal: " . mysqli_error($conn);
}