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
        
        <title>Dashboard - Budget Tracker</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Include jQuery -->
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">

            <!-- Logout Button -->
            <button onclick="return confirm('Are you sure you want to log out?') ? window.location.href='logout.php' : false;">Logout</button>

            <h2>Welcome to Your Budget Tracker</h2>

            <p>View and manage your expenses and budget here.</p>

            <!-- Success or Error Messages -->
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

            <!-- Monthly and Yearly Summary Links -->
            <div class="summary-links">
                <a href="add_category.php" class="summary-link">Add Category</a>
                <a href="monthly_summary.php" class="summary-link">View Monthly Summary</a>
                <a href="yearly_summary.php" class="summary-link">View Yearly Summary</a>
                <a href="yearly_bargraph.php" class="summary-link">View Yearly Summary Bar Graph</a>
            </div>
            

            <h3>Your Budget</h3>

            <!-- Add Expense Form -->
            <form id="add-expense-form" action="add_expense.php" method="post">
                
                <label for="expenseName">Expense Name:</label>
                <input type="text" id="expenseName" name="expenseName" required><br>

                <label for="expenseAmount">Amount:</label>
                <input type="number" id="expenseAmount" name="expenseAmount" required step="0.01"><br>

                <label for="expenseDate">Date:</label>
                <input type="date" id="expenseDate" name="expenseDate" required><br>

                <label for="category">Category:</label>
                <select name="category" id="category" required>
                    <!-- Categories will be loaded here by AJAX -->
                </select><br>

                <button type="submit">Add Expense</button>
                
            </form>

            <!-- Display Current Expenses -->
            <h3>Current Expenses</h3>

            <table>
                <thead>
                    <tr>
                        <th>Expense Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="expenses-table-body">
                    <!-- Populated by AJAX -->
                </tbody>
            </table>

        </div>

        <script>
            //**  AJAX SCRIPTS **
            function loadExpenses() {
                $.ajax({
                    url: 'ajax_expenses.php', // Path to your AJAX PHP file
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            var tbody = $('#expenses-table-body');
                            tbody.empty(); // Clear any existing rows

                            if (response.expenses.length === 0) {
                                tbody.append("<tr><td colspan='5'>No expenses recorded yet.</td></tr>");
                            } else {
                                response.expenses.forEach(function(expense) {
                                    var date = new Date(expense.expense_date);
                                    var formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                                    tbody.append("<tr>" +
                                        "<td>" + expense.expense_name + "</td>" +
                                        "<td>$ " + expense.amount + "</td>" +
                                        "<td>" + formattedDate + "</td>" +
                                        "<td>" + expense.category_name + "</td>" +
                                        "<td><a href='delete_expense.php?id=" + expense.expense_id + "' onclick=\"return confirm('Are you sure you want to delete this expense?');\">Delete</a></td>" +
                                        "</tr>");
                                });
                            }
                        }
                    },
                    error: function() {
                        alert("There was an error loading expenses.");
                    }
                });
            }

            
            function loadCategories() {
                $.ajax({
                    url: 'ajax_categories.php', // Path to the category AJAX PHP file
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            var select = $('#category');
                            select.empty(); // Clear any existing options

                            response.categories.forEach(function(category) {
                                select.append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
                            });
                        }
                    },
                    error: function() {
                        alert("There was an error loading categories.");
                    }
                });
            }

            
            $(document).ready(function() {
                loadExpenses();
                loadCategories();
                $('#add-expense-form').on('submit', function(event) {
                    event.preventDefault(); 

                    // Gathering form data
                    var formData = {
                        expenseName: $('#expenseName').val(),
                        expenseAmount: $('#expenseAmount').val(),
                        expenseDate: $('#expenseDate').val(),
                        category: $('#category').val()
                    };

                    
                    $.ajax({
                        url: 'add_expense.php',
                        method: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.error) {
                                alert(response.error);
                            } else if (response.success) {
                                $('#add-expense-form')[0].reset();
                                loadExpenses(); // Reload the expenses table
                            } 
                        },
                        error: function() {
                            alert('There was an error submitting the expense.');
                        }
                    });
                });
            });
        </script>
        
        
    </body>
</html>
