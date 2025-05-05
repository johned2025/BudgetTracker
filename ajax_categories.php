<?php
session_start();
header('Content-Type: application/json');
// MySQLi Connection Logic
try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Fetch categories
$query = "SELECT * FROM categories";
$result_cat = mysqli_query($mysqli, $query);

if (!$result_cat) {
    echo json_encode(['error' => 'Query failed']);
    exit();
}

$categories = [];
while ($info = mysqli_fetch_array($result_cat)) {
    $categories[] = $info;
}

echo json_encode(['categories' => $categories]);
?>
