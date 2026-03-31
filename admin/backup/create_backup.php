<?php
require_once "../../config/database.php";

$backupDir = "backups/";

if(!is_dir($backupDir)){
    mkdir($backupDir,0777,true);
}

$filename = $backupDir . "backup_" . date("Y-m-d_H-i-s") . ".sql";

$tables = $conn->query("SHOW TABLES");

$fp = fopen($filename,"w");

while($table = $tables->fetch_array()){
    $tableName = $table[0];

    $create = $conn->query("SHOW CREATE TABLE $tableName")->fetch_assoc();
    fwrite($fp, $create['Create Table'].";\n\n");

    $rows = $conn->query("SELECT * FROM $tableName");

    while($row = $rows->fetch_assoc()){
       $values = array_map(function($v) use ($conn) {
    return "'" . $conn->real_escape_string($v) . "'";
}, array_values($row));
fwrite($fp,"INSERT INTO $tableName VALUES(".implode(",",$values).");\n");
    }

    fwrite($fp,"\n\n");
}

fclose($fp);

header("Location: backup.php");