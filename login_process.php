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
    
    // Get form data and sanitize inputs
    $username = trim(filter_input(INPUT_POST, 'username'));
    $password = trim(filter_input(INPUT_POST, 'password'));

    // Validate inputs
    if ((!filter_input(INPUT_POST, 'username')) || (!filter_input(INPUT_POST, 'password'))) {
        $_SESSION['error_message'] = "Both fields are required!";
        header("Location: login.php");
        exit;
    }
    
    try{
        
        // Create and execute a query
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = SHA1('$password')";

        $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

        //get the number of rows in the result set; should be 1 if a match
        if (mysqli_num_rows($result) == 1) {

            //if authorized, get the values of f_name l_name
            while ($info = mysqli_fetch_array($result)) {
                $_SESSION['user_id'] = stripslashes($info['user_id']);
                $_SESSION['username'] = stripslashes($info['username']);
            }

            header('Location: dashboard.php');
            exit();

        } else {
            //redirect back to login form if not authorized
            $_SESSION['error_message'] = "Invalid credentials!";
            header('Location: login.php');
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: login.php');
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
