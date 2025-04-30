<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

/* Check the connection. */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


// Check if expense_id is set and is valid
if (isset($_GET['id'])  && is_numeric($_GET['id'])) {
    $expense_id = filter_input(INPUT_GET, 'id');
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    
    try {
        // Prepare the SQL statement to delete the expense
        $sql = "DELETE FROM expenses WHERE expense_id = $expense_id AND user_id = $user_id";
        
        $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
        
        if (mysqli_query($mysqli, $sql)) {
            
            $_SESSION['message'] = "Expense deleted successfully!";
            header('Location: dashboard.php');
            exit();
            
        } else {
            throw new Exception("Error executing query: " . mysqli_error($mysqli));
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: dashboard.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
    header('Location: dashboard.php');
    exit();
}
