<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
session_start();
header('Content-Type: application/json');
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// MySQLi Connection Logic
try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Fetch expenses for the logged-in user with category names
$user_id = $_SESSION['user_id'];

$sql = "
    SELECT e.expense_id, e.expense_name, e.amount, e.expense_date, c.category_name 
    FROM expenses e
    JOIN categories c ON e.category_id = c.category_id
    WHERE e.user_id = ?
    ORDER BY e.expense_date DESC
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Query failed']);
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    echo json_encode(['error' => 'Execution failed']);
    exit();
}
$expenses = [];
while ($info = $result->fetch_assoc()) {
    $expenses[] = $info;
}
$stmt->close();
echo json_encode(['expenses' => $expenses]);
?>
