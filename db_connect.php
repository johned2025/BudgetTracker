<?php
require_once 'db_config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    throw new Exception("Database connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>
