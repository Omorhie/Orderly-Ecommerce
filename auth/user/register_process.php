<?php
include "../../config/database.php";

$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$query = mysqli_query($conn,"INSERT INTO users(username,email,phone,password)
VALUES('$username','$email','$phone','$password')");

if($query){
    echo "Register berhasil";
}else{
    echo "Register gagal";
}