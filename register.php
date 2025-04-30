<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Register - Budget Tracker</title>
        
        <link rel="stylesheet" href="style.css">
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Create an Account</h2>
            
            <?php
            
            if (isset($_SESSION['error_message'])) {
                echo "<p class='error' style='color : red; font-weight: bold;'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']); // Clear the error after displaying
            }
            
            ?>
            
            <form action="register_process.php" method="post" id="registerForm">
                
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>

                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required><br>

                <button type="submit">Register</button>
                
            </form>

            <p><a href="login.php">Already have an account? Login here</a></p>
        </div>
    </body>
</html>
