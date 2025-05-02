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
require_once 'db_connect.php';

// Fetch all categories
$query = "SELECT * FROM categories";
$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

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
        
        $sql_cat = "DELETE FROM categories WHERE category_id = $delete_id";
        
        $result_cat = mysqli_query($mysqli, $sql_cat) or die(mysqli_error($mysqli));
        
        if (mysqli_query($mysqli, $sql_cat)) {
            
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


<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Add Category - Budget Tracker</title>
        
        <link rel="stylesheet" href="style.css">
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Add New Category</h2>

            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<p class='error' style='color : red; font-weight: bold;'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']);
            }
            if (isset($_SESSION['message'])) {
                echo "<p class='message' style='color : #d4af37; font-weight: bold;'>{$_SESSION['message']}</p>";
                unset($_SESSION['message']);
            }
            ?>

            <form action="add_category_process.php" method="post">
                
                <label for="categoryName">Category Name:</label>
                <input type="text" id="categoryName" name="categoryName" required><br>

                <button type="submit">Add Category</button>
                
            </form>

            <p><a href="dashboard.php">Back to Dashboard</a></p>

            <!-- Table of Categories -->
            <h3>Categories</h3>
            
            <?php if (empty($categories)) { ?>
                <p>No categories available.</p>
            <?php } else { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category) { ?>
                            <tr>
                                <td><?php echo $category['category_name']; ?></td>
                                <td>
                                    <a href="add_category.php?delete_id=<?php echo $category['category_id']; ?>" onclick="return confirm('Are you sure you want to delete this category?\n\nNote: Deleting this category will also delete expenses data.');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </body>
</html>

