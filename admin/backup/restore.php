<?php
require_once "../../config/database.php";

if(isset($_FILES['backup_file'])){

    $file = $_FILES['backup_file']['tmp_name'];
    $sql = file_get_contents($file);

    mysqli_report(MYSQLI_REPORT_OFF);

    $queries = explode(";", $sql);

    $success = 0;
    $skipped = 0;
    $error   = 0;

    foreach($queries as $query){

        $query = trim($query);
        if(empty($query)) continue;

        if($conn->query($query)){
            $success++;
        } else {

            if(strpos($conn->error, "already exists") !== false){
                $skipped++;
            } else {
                $error++;
            }
        }
    }

    // ✅ NOTIFIKASI SESUAI KONDISI
    if($success > 0){
        echo "<script>
            alert('Restore berhasil!');
            window.location='../dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Restore gagal atau tidak ada data yang diproses!');
            window.location='backup.php';
        </script>";
    }
}
?>