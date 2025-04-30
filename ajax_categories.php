<?php
session_start();

// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

// Check the connection
if (mysqli_connect_errno()) {
    echo json_encode(['error' => 'Database connection failed']);
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
