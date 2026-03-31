<?php
require_once "../../config/database.php";

if(isset($_FILES['backup_file'])){

    $file = $_FILES['backup_file']['tmp_name'];
    $sql = file_get_contents($file);

    if($conn->multi_query($sql)){
        echo "Restore berhasil";
    }else{
        echo "Error restore: " . $conn->error;
    }
}