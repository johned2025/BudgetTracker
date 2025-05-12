# Budget Tracker

A web-based application that enables users to track their expenses, categorize spending, and visualize financial data over time. Built with the LAMP stack, it offers a secure and customizable environment for personal budgeting.
## Developer Notes

This project is also intended as a demonstration of software engineering principles and refactoring skills. Key improvements and design decisions include:

- **Separation of Concerns**: The application structure separates presentation logic, business logic, and data access logic for improved readability and maintainability.
- **Security Enhancements**:
  - Database credentials are hidden by isolating them in a dedicated `dbconfig.php` file, excluded from version control.
  - Use of prepared statements throughout the project to prevent SQL injection.
- **Code Reusability**:
  - Reusable SQL models (functions or classes) are used to centralize and streamline database operations.
  - Common patterns are abstracted to avoid redundancy and promote modularity.
## Features

- **User Authentication**: Secure account creation and login using PHP's `password_hash()` and `password_verify()` functions.
- **Expense Management**: Add, edit, and delete expenses with associated categories and timestamps.
- **Custom Categories**: Personalize expense categories to suit individual budgeting needs.
- **Summarized Reports**: View monthly and yearly summaries of expenses.
- **Data Visualization**: Interactive charts displaying expenses by category using Chart.js.
- **Security Measures**: Utilizes prepared statements to safeguard against SQL injection attacks.

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Server**: Apache on Linux

## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/johned2025/BudgetTracker.git

   
2. **Set Up the Database**

Import the provided SQL schema into your MySQL database:

```bash
mysql -u username -p database_name < schema.sql
```
Replace `username` and `database_name` with your MySQL credentials.

3.**Deploy to Server**
Move the project files to your web server's root directory (e.g., /var/www/html/).

## Configure the Application

Update database connection settings by creating the file `dbconfig.php` and adding your own databse information to this template:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_database');
```

## Access the Application
Navigate to http://localhost/budget-tracker in your web browser.

## Limitations
- Responsive Design: The current interface is not optimized for mobile devices.

- Feature Set: Focuses solely on expense tracking; income tracking and budgeting features are not included.

## Future Enhancements
- Implement responsive design for better mobile usability.

- Add income tracking and budgeting capabilities.

- Enable data export to CSV or PDF formats.

- Introduce user-defined budget limits with alerts for overspending.




