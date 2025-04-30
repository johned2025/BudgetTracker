<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

// Check the connection
if (mysqli_connect_errno()) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Fetch expenses for the logged-in user with category names
$user_id = $_SESSION['user_id'];

$sql = "
    SELECT e.expense_id, e.expense_name, e.amount, e.expense_date, c.category_name 
    FROM expenses e
    JOIN categories c ON e.category_id = c.category_id
    WHERE e.user_id = '$user_id'
    ORDER BY e.expense_date DESC
";

$result = mysqli_query($mysqli, $sql);

if (!$result) {
    echo json_encode(['error' => 'Query failed']);
    exit();
}

$expenses = [];
while ($info = mysqli_fetch_array($result)) {
    $expenses[] = $info;
}

echo json_encode(['expenses' => $expenses]);
?>
