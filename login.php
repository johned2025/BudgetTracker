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

        <title>Login - Budget Tracker</title>

        <link rel="stylesheet" href="style.css">

    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Login to Your Account</h2>

            <?php
            
            if (isset($_SESSION['error_message'])) {
                echo "<p class='error' style='color : red; font-weight: bold;'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']); // Clear the error after displaying
            }
            
            if (isset($_SESSION['message'])) {
                echo "<p class='message' style='color : #d4af37; font-weight: bold;'>{$_SESSION['message']}</p>";
                unset($_SESSION['message']); // Clear the error after displaying
            }
            
            ?>

            <form action="login_process.php" method="post" id="loginForm">

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>

                <button type="submit">Login</button>

            </form>

            <p><a href="register.php">Create a new account</a></p>

        </div>
    </body>
</html>
