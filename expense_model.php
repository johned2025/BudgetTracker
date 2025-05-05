<?php
function getAvailableMonths($mysqli, $user_id, $year) {
    $query = "
        SELECT DISTINCT MONTH(expense_date) AS month
        FROM expenses
        WHERE user_id = ? AND YEAR(expense_date) = ?
        ORDER BY month ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $user_id, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $months = [];
    while ($row = $result->fetch_assoc()) {
        $months[] = $row;
    }
    return $months;
}
function getAvailableYears($mysqli, $user_id) {
    $query = "
        SELECT DISTINCT YEAR(expense_date) AS year
        FROM expenses
        WHERE user_id = ?
        ORDER BY year DESC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $years = [];
    while ($row = $result->fetch_assoc()) {
        $years[] = $row;
    }
    return $years;
}
function getExpensesByMonth($mysqli, $user_id, $year, $month) {
    $query = "
        SELECT expense_name, amount, expense_date
        FROM expenses
        WHERE user_id = ? AND YEAR(expense_date) = ? AND MONTH(expense_date) = ?
        ORDER BY expense_date DESC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iii", $user_id, $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    return $expenses;
}