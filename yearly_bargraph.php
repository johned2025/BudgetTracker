<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}


// MySQLi Connection Logic
$mysqli = mysqli_connect("localhost", "cs213user", "letmein", "budgetDB");

/* Check the connection. */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


$user_id = $_SESSION['user_id'];

// Get the selected year from the form (if not set, default to the current year)
$year = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Query to get the total expense per category for the selected year
$query = "
    SELECT c.category_name, SUM(e.amount) AS total_expense
    FROM expenses e
    JOIN categories c ON e.category_id = c.category_id
    WHERE YEAR(e.expense_date) = '$year'
    AND e.user_id = '$user_id'
    GROUP BY c.category_name
";
$categories_expenses = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli)); 
$categories = [];
$totals = [];

while ($row = mysqli_fetch_array($categories_expenses, MYSQLI_ASSOC)) {
    $categories[] = $row['category_name'];
    $totals[] = $row['total_expense'];
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Yearly Bar Graph - Budget Tracker</title>
        
        <link rel="stylesheet" href="style.css">
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Yearly Expense Bar Graph for <?php echo $year; ?></h2>

            <!-- Yearly Summary Form -->
            <form action="yearly_bargraph.php" method="post">
                
                <label for="year">Select Year:</label>
                <select name="year" id="year" onchange="this.form.submit()">
                    <?php 
                    // Generate years dynamically based on available data
                    $year_query = "SELECT DISTINCT YEAR(expense_date) AS year FROM expenses WHERE user_id = '$user_id' ORDER BY year DESC";
                    $years= mysqli_query($mysqli, $year_query) or die(mysqli_error($mysqli)); 

                    foreach ($years as $row) {
                        $selected = ($row['year'] == $year) ? 'selected' : '';
                        echo "<option value='{$row['year']}' {$selected}>{$row['year']}</option>";
                    }
                    ?>
                </select>
                
            </form>

            <!-- Bar Chart for Yearly Summary -->
            <canvas id="yearlyChart" width="400" height="200"></canvas>

            <script>
                var ctx = document.getElementById('yearlyChart').getContext('2d');
                var yearlyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($categories); ?>, // Categories
                        datasets: [{
                            label: 'Total Expenses in <?php echo htmlspecialchars($year); ?>',
                            data: <?php echo json_encode($totals); ?>, // Expense amounts
                            backgroundColor: 'rgba(255, 215, 0, 0.2)', // Bar color
                            borderColor: 'rgba(255, 215, 0, 1)', // Border color
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>

            <!-- Button to return to dashboard -->
            <a href="dashboard.php" class="button">Return to Dashboard</a>

        </div>
    </body>
</html>
