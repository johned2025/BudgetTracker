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

            
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                fetch('ajax_categories.php') 
                    .then(response => response.json())
                    .then(data => {
                        const container = document.querySelector(".container");
                        const tableSection = document.createElement("div");
                        tableSection.innerHTML = "<h3>Categories</h3>";

                        if (!data.categories || data.categories.length === 0) {
                            tableSection.innerHTML += "<p>No categories available.</p>";
                        } else {
                            const table = document.createElement("table");
                            const thead = document.createElement("thead");
                            thead.innerHTML = `
                                <tr>
                                    <th>Category Name</th>
                                    <th>Action</th>
                                </tr>`;
                            table.appendChild(thead);

                            const tbody = document.createElement("tbody");

                            data.categories.forEach(cat => {
                                const row = document.createElement("tr");

                                const nameCell = document.createElement("td");
                                nameCell.textContent = cat.category_name;

                                const actionCell = document.createElement("td");
                                const delLink = document.createElement("a");
                                delLink.href = "delete_category.php?delete_id=" + cat.category_id;
                                delLink.textContent = "Delete";
                                delLink.onclick = function () {
                                    return confirm('Are you sure you want to delete this category?\n\nIMPORTANT NOTE! Deleting this category will also delete expenses data.');
                                };

                                actionCell.appendChild(delLink);
                                row.appendChild(nameCell);
                                row.appendChild(actionCell);

                                tbody.appendChild(row);
                            });

                            table.appendChild(tbody);
                            tableSection.appendChild(table);
                        }

                        container.appendChild(tableSection);
                    })
                    .catch(error => {
                        console.error("Error fetching categories:", error);
                    });
            });
        </script>

    </body>
</html>

