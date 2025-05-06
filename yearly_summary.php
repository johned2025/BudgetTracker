<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}


try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage()); 
}
require_once 'expense_model.php';
$user_id = $_SESSION['user_id'];

// Get the current year as default value
$current_year = date('Y');

$filter_year = isset($_POST['year']) ? $_POST['year'] : $current_year;

// Query to fetch distinct years from the expenses table for the logged-in user
$years = getAvailableYears($mysqli, $user_id);

// Query to fetch the total expenses for the selected year
$total_yearly_amount = getTotalYearlyAmount( $mysqli, $user_id, $filter_year);

// Query to fetch monthly expenses for the selected year
$yearly_expenses = getYearlyExpensesSummary( $mysqli, $user_id, $filter_year);

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
