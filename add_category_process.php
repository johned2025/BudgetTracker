<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

// Check the connection. 
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = trim(filter_input(INPUT_POST, 'categoryName'));

    // Validate input
    if (!filter_input(INPUT_POST, 'categoryName')) {
        $_SESSION['error_message'] = "Category name is required!";
        header('Location: add_category.php');
        exit();
    }

    // Sanitize input to prevent SQL injection
    $category_name = mysqli_real_escape_string($mysqli, $categoryName); 

    try {
        $sql = "SELECT * FROM categories WHERE category_name = '$categoryName'";
        
        $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
        
        if (mysqli_num_rows($result) >= 1) {
            $_SESSION['error_message'] = "Category already exist!";
            header('Location: add_category.php');
            exit();
        }
        
        $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        
        // Execute the query
        if (mysqli_query($mysqli, $query)) {
            $_SESSION['message'] = "Category added successfully!";
            header('Location: dashboard.php');
            exit();
        } else {
            throw new Exception("Error executing query: " . mysqli_error($mysqli));
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error occurred: " . $e->getMessage();
        header('Location: add_category.php');
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
