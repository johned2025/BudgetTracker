<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// MySQLi Connection 
require_once 'db_connect.php';


// Check if delete action is triggered
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    try {
        // Prepare the SQL statement to delete the category
        $stmt =$mysqli->prepare( "DELETE FROM expenses WHERE category_id = ?");
        
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        
        $sql_cat =$mysqli->prepare("DELETE FROM categories WHERE category_id = ?");
        
        $sql_cat->bind_param("i", $delete_id);
        
        if ($sql_cat->execute()) {
            
            $_SESSION['message'] = "Category deleted successfully!";
            header('Location: add_category.php');
            exit();
            
        } else {
            throw new Exception("Error executing query: " . mysqli_error($mysqli));
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: add_category.php');
        exit();
    }
}

// Close the database connection
mysqli_close($mysqli);
?>