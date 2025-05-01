<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


// MySQLi Connection Logic
require_once 'db_config.php';
$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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
        
        // Create prepared statement
        $sql = $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND password = SHA1(?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        //binding stamtement with user input
        $stmt->bind_param("ss", $username, $password);
        //
        $stmt->execute();
        //
        $result = $stmt->get_result();
        

        //get the number of rows in the result set; should be 1 if a match
        if ($result->num_rows == 1) {

            //if authorized, get the values of f_name l_name
            $info = $result->fetch_assoc();  

            $_SESSION['user_id'] = $info['user_id'];
            $_SESSION['username'] = $info['username'];

            header('Location: dashboard.php');
            exit();

        } else {
            //redirect back to login form if not authorized
            $_SESSION['error_message'] = "Invalid credentials!";
            header('Location: login.php');
            exit();
        }
        $stmt->close();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header('Location: login.php');
        exit();
    }
}

// Close database connection
mysqli_close($mysqli);
?>
