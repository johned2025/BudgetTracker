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
function getYearlyExpensesSummary(mysqli $mysqli, int $user_id, int $filter_year): array {
    $query = "
        SELECT MONTH(expense_date) AS month, SUM(amount) AS total_amount
        FROM expenses
        WHERE user_id = ?
        AND YEAR(expense_date) = ?
        GROUP BY MONTH(expense_date)
        ORDER BY month ASC
    ";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $user_id, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $summary = [];
    while ($row = $result->fetch_assoc()) {
        $summary[] = $row;
    }

    $stmt->close();
    return $summary;
}
function getTotalYearlyAmount(mysqli $mysqli, int $user_id, int $filter_year): float {
    $query = "
        SELECT SUM(amount) AS total_yearly_amount
        FROM expenses
        WHERE user_id = ?
        AND YEAR(expense_date) = ?
    ";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $user_id, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $total = $row['total_yearly_amount'] ?? 0;

    $stmt->close();
    return (float)$total;
}
function getCategoryExpensesByYear($mysqli, $user_id, $year) {
    $query = "
        SELECT c.category_name, SUM(e.amount) AS total_expense
        FROM expenses e
        JOIN categories c ON e.category_id = c.category_id
        WHERE YEAR(e.expense_date) = ?
        AND e.user_id = ?
        GROUP BY c.category_name
    ";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $year, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    $totals = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category_name'];
        $totals[] = $row['total_expense'];
    }

    return ['categories' => $categories, 'totals' => $totals];
}


