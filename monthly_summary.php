<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
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

// Get the current year as the default value
$current_year = date('Y');
$current_month = date('m');

// Set the filter year and month based on POST, default to the current year and month
$filter_year = isset($_POST['year']) ? $_POST['year'] : $current_year;
$filter_month = isset($_POST['month']) ? $_POST['month'] : $current_month;

// Query to fetch distinct months for the selected year
$query_months = "
    SELECT DISTINCT MONTH(expense_date) AS month
    FROM expenses
    WHERE user_id = '$user_id' AND YEAR(expense_date) = '$filter_year'
    ORDER BY month ASC
";

$result_months = mysqli_query($mysqli, $query_months) or die(mysqli_error($mysqli)); // Execute the query
$months = [];

while ($row = mysqli_fetch_array($result_months, MYSQLI_ASSOC)) { // Fetch rows
    $months[] = $row;
}

// Query to fetch the expenses for the selected month and year
$query_expenses = "
    SELECT expense_name, amount, expense_date
    FROM expenses
    WHERE user_id = '$user_id' AND YEAR(expense_date) = '$filter_year' AND MONTH(expense_date) = '$filter_month'
    ORDER BY expense_date DESC
";

$result_expenses = mysqli_query($mysqli, $query_expenses) or die(mysqli_error($mysqli)); // Execute the query
$monthly_expenses = [];

while ($row = mysqli_fetch_array($result_expenses, MYSQLI_ASSOC)) { // Fetch rows
    $monthly_expenses[] = $row;
}

// Calculate the total amount for the selected month
$total_monthly_amount = 0;
foreach ($monthly_expenses as $expense) {
    $total_monthly_amount += $expense['amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Monthly Summary - Budget Tracker</title>
        
        <link rel="stylesheet" href="style.css">
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Monthly Expense Summary</h2>

            <!-- Filter Form for Year and Month -->
            <form action="monthly_summary.php" method="post">
                
                <label for="year">Select Year:</label>
                <select name="year" id="year">
                    <?php
                        // Fetch distinct years from the database (for the user's expenses)
                        $query_years = "
                            SELECT DISTINCT YEAR(expense_date) AS year
                            FROM expenses
                            WHERE user_id = '$user_id'
                            ORDER BY year DESC
                        ";

                        $result_years = mysqli_query($mysqli, $query_years) or die(mysqli_error($mysqli)); // Execute the query
                        $years = [];

                        while ($row = mysqli_fetch_array($result_years, MYSQLI_ASSOC)) { // Fetch rows
                            $years[] = $row;
                        }

                        foreach ($years as $year) {
                            echo "<option value=\"{$year['year']}\" " . ($filter_year == $year['year'] ? 'selected' : '') . ">{$year['year']}</option>";
                        }
                    ?>
                </select>

                <label for="month">Select Month:</label>
                <select name="month" id="month">
                    
                <?php
                    // Loop through the months and set the selected option dynamically
                    for ($m = 1; $m <= 12; $m++) {
                        $month_name = date("F", mktime(0, 0, 0, $m, 1)); // Get the full month name (January, February, etc.)
                        echo "<option value=\"$m\" " . ($filter_month == $m ? 'selected' : '') . ">$month_name</option>";
                    }
                ?>
                    
                </select>

                <button type="submit">Filter</button>
            </form>

            <!-- Display Monthly Expenses -->
            <h3>Expenses for <?= date("F", mktime(0, 0, 0, $filter_month, 10)) ?>, <?= $filter_year ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Expense Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if ($monthly_expenses) { ?>
                        <?php foreach ($monthly_expenses as $expense) { ?>
                            <tr>
                                <td><?= $expense['expense_name'] ?></td>
                                <td>$<?= number_format($expense['amount'], 2) ?></td>
                                <td><?= date("F j, Y", strtotime($expense['expense_date'])) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No expenses recorded for this month.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Display Total Expenses for the Month -->
            <h3>Total Expenses for <?= date("F", mktime(0, 0, 0, $filter_month, 10)) ?>, <?= $filter_year ?>: $<?= number_format($total_monthly_amount, 2) ?></h3>

            <!-- Button to return to dashboard -->
            <a href="dashboard.php" class="button">Return to Dashboard</a>
            
        </div>
    </body>
</html>
