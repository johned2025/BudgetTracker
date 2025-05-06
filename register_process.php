<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


// MySQLi Connection Logic
try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage()); 
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data and sanitize
    $username = trim(filter_input(INPUT_POST, 'username'));
    $email = trim(filter_input(INPUT_POST, 'email'));
    $password = trim(filter_input(INPUT_POST, 'password'));
    $confirmPassword = trim(filter_input(INPUT_POST, 'confirmPassword'));

    // Validate inputs
    if ((!filter_input(INPUT_POST, 'username')) || (!filter_input(INPUT_POST, 'email')) ||
            (!filter_input(INPUT_POST, 'password')) || (!filter_input(INPUT_POST, 'confirmPassword'))) {
        $_SESSION['error_message'] = "All fields are required!";
        header('Location: register.php');
        exit();
    }

    if ($password !== $confirmPassword) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header('Location: register.php');
        exit();
    }

    // Check if username already exists
    try {
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        
        $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
        
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['error_message'] = "Username already taken!";
            header('Location: register.php');
            exit();
        }

        // Insert new user into database
        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', SHA1('$password'))";
        
        if (mysqli_query($mysqli, $query)) {
            
            $_SESSION['message'] = "Registration successful! You can now log in.";
            header('Location: login.php');
            exit();
            
        } else {
            throw new Exception("Error executing query: " . mysqli_error($mysqli));
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: register.php');
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
