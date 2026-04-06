<?php
require_once "../../config/database.php";

$id = $_GET['id'];

$conn->query("DELETE FROM officer WHERE id=$id");

header("Location: index.php");
