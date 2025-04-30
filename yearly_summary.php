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

// Get the current year as default value
$current_year = date('Y');

$filter_year = isset($_POST['year']) ? $_POST['year'] : $current_year;

// Query to fetch distinct years from the expenses table for the logged-in user
$query_years = "
    SELECT DISTINCT YEAR(expense_date) AS year
    FROM expenses
    WHERE user_id = '$user_id'
    ORDER BY year DESC
";

$result_years = mysqli_query($mysqli, $query_years) or die(mysqli_error($mysqli)); //!! Corrected variable
$years = [];

while ($row = mysqli_fetch_array($result_years, MYSQLI_ASSOC)) {
    $years[] = $row;
}

// Query to fetch the total expenses for the selected year
$query_total_yearly = "
    SELECT SUM(amount) AS total_yearly_amount
    FROM expenses
    WHERE user_id = '$user_id'
    AND YEAR(expense_date) = $filter_year
";

$result_total_yearly = mysqli_query($mysqli, $query_total_yearly) or die(mysqli_error($mysqli));
$row_total_yearly = mysqli_fetch_assoc($result_total_yearly);
$total_yearly_amount = $row_total_yearly['total_yearly_amount'] ?? 0;

// Query to fetch monthly expenses for the selected year
$query_monthly_expenses = "
    SELECT MONTH(expense_date) AS month, SUM(amount) AS total_amount
    FROM expenses
    WHERE user_id = '$user_id'
    AND YEAR(expense_date) = '$filter_year'
    GROUP BY MONTH(expense_date)
    ORDER BY month ASC
"; //!! Added query to fetch monthly expenses

$result_monthly_expenses = mysqli_query($mysqli, $query_monthly_expenses) or die(mysqli_error($mysqli));
$yearly_expenses = [];

while ($row = mysqli_fetch_array($result_monthly_expenses, MYSQLI_ASSOC)) {
    $yearly_expenses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Yearly Summary - Budget Tracker</title>
        
        <link rel="stylesheet" href="style.css">
        
    </head>
    
    <body>
        <img src='images/Gold.gif'>
        
        <div class="container">
            
            <h2>Yearly Expense Summary</h2>

            <!-- Filter Form for Year -->
            <form action="yearly_summary.php" method="post">
                
                <label for="year">Select Year:</label>
                <select name="year" id="year">
                    <?php foreach ($years as $year) { ?>
                    
                        <option value="<?php echo $year['year'] ?>" <?php echo $filter_year == $year['year'] ? 'selected' : '' ?>>
                            <?php echo $year['year'] ?>
                        </option>
                        
                    <?php } ?>
                </select>

                <button type="submit">Filter</button>
                
            </form>

            <!-- Display Yearly Expenses -->
            <h3>Expenses for the Year <?php echo $filter_year ?></h3>
            
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Expense</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if ($yearly_expenses) { ?>
                        <?php foreach ($yearly_expenses as $expense) { ?>
                    
                            <tr>
                                <td><?php echo date("F", mktime(0, 0, 0, $expense['month'], 10)) ?></td>
                                <td>$<?php echo number_format($expense['total_amount'], 2) ?></td>
                            </tr>
                            
                        <?php } ?>
                    <?php } else { ?>
                            
                        <tr>
                            <td colspan="2">No expenses recorded for this year.</td>
                        </tr>
                        
                    <?php } ?>
                </tbody>
            </table>

            <!-- Display Total Expenses for the Year -->
            <h3>Total Expenses for <?php echo $filter_year ?>: $<?php echo number_format($total_yearly_amount, 2) ?></h3>

            <!-- Button to return to dashboard -->
            <a href="dashboard.php" class="button">Return to Dashboard</a>
            
        </div>
    </body>
</html>
