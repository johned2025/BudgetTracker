<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


// MySQLi Connection Logic
require_once 'db_connect.php';

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
        // Prepare the SQL statement
        $stmt = $mysqli->prepare("SELECT * FROM categories WHERE category_name = ?");
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("s", $categoryName);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the category already exists
        if ($result->num_rows >= 1) {
            $_SESSION['error_message'] = "Category already exists!";
            header('Location: add_category.php');
            exit();
        }
        $stmt->close();

        
        $query = $mysqli->prepare( "INSERT INTO categories (category_name) VALUES (?)");
        $query->bind_param("s", $categoryName); 
        
        // Execute the query
        if ($query->execute()) {
            $_SESSION['message'] = "Category added successfully!";
            header('Location: add_category.php');
            exit();
        } else {
            throw new Exception("Error executing query: " . $query->error);
        }
        $query->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error occurred: " . $e->getMessage();
        header('Location: add_category.php');
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
