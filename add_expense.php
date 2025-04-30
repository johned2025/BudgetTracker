<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json');
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit();
}


// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

// Check the connection. 
if (mysqli_connect_errno()) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $expense_name = trim(filter_input(INPUT_POST, 'expenseName'));
    $amount = trim(filter_input(INPUT_POST, 'expenseAmount'));
    $expense_date = filter_input(INPUT_POST, 'expenseDate');
    $category = trim(filter_input(INPUT_POST, 'category'));
    $user_id = $_SESSION['user_id'];

    // Validate the form data
    if (empty($expense_name) || empty($amount) || empty($expense_date) || empty($category)) {
        echo json_encode(['error' => 'All fields are required!']);
        exit();
    }

    $timestamp = strtotime(str_replace('/', '-', $expense_date));
    $formatted_date = date('Y-m-d', $timestamp);
    
    try {
        // Fetch categories for the dropdown and ensure it's safe to use
        $query_cat = "SELECT * FROM categories WHERE category_id = ?";
        $stmt_cat = mysqli_prepare($mysqli, $query_cat);
        mysqli_stmt_bind_param($stmt_cat, 'i', $category);
        mysqli_stmt_execute($stmt_cat);
        $result_cat = mysqli_stmt_get_result($stmt_cat);

        // Check if the category exists
        if (mysqli_num_rows($result_cat) == 0) {
            echo json_encode(['error' => 'Invalid category selected!']);
            exit();
        }
        
        // Insert the expense into the database using a prepared statement to avoid SQL injection
        $query = "INSERT INTO expenses (user_id, expense_name, amount, expense_date, category_id) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($mysqli, $query);
        mysqli_stmt_bind_param($stmt, 'isdsd', $user_id, $expense_name, $amount, $formatted_date, $category);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Expense added successfully!";
            echo json_encode(['success' => 'Expense added successfully!']);
            exit();
        } else {
            throw new Exception("Error executing query: " . mysqli_error($mysqli));
        }
        
    } catch (PDOException $e) {
        
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
