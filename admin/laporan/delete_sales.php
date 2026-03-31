<?php
require_once "../../config/database.php";

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    $conn->query("DELETE FROM transactions WHERE id = $id");
}

header("Location: index.php");
exit;