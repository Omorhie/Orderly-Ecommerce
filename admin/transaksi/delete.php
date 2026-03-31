<?php
require_once "../../config/database.php";

$id = $_GET['id'];

$conn->query("DELETE FROM transactions WHERE id=$id");

header("Location: index.php");
