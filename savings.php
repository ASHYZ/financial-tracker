<?php
session_start();

// Include database configuration
include_once 'db_config.php'; // Ensure this path is correct and points to your db_config file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please log in to continue.");
}

// Continue with your existing code
$userID = $_SESSION['user_id'];

// Initialize totals
$totalExpenses = $totalInvestments = $totalLoans = $totalIncome = 0;

// Function to fetch data with error handling
function fetchData($query, $params, $conn) {
    $stmt = $conn->prepare($query);
    if ($stmt) {
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        return false; // Query preparation failed
    }
}

// Fetch total expenses for the current month
$queryExpenses = "SELECT SUM(Amount) AS TotalExpenses 
                  FROM expenses 
                  WHERE UserID = :user_id 
                    AND YEAR(ExpenseDate) = YEAR(CURDATE()) 
                    AND MONTH(ExpenseDate) = MONTH(CURDATE())";
$expenses = fetchData($queryExpenses, [':user_id' => $userID], $conn);
if ($expenses !== false && $expenses['TotalExpenses'] !== null) {
    $totalExpenses = $expenses['TotalExpenses'];
}

// Fetch total investments for the current month
$queryInvestments = "SELECT SUM(Amount) AS TotalInvestments 
                     FROM investments 
                     WHERE UserID = :user_id 
                       AND YEAR(InvestmentDate) = YEAR(CURDATE()) 
                       AND MONTH(InvestmentDate) = MONTH(CURDATE())";
$investments = fetchData($queryInvestments, [':user_id' => $userID], $conn);
if ($investments !== false && $investments['TotalInvestments'] !== null) {
    $totalInvestments = $investments['TotalInvestments'];
}

// Fetch total loans (EMI payments) for the current month
$queryLoans = "SELECT SUM(EMIAmount) AS TotalLoans 
               FROM loans 
               WHERE UserID = :user_id 
                 AND YEAR(StartDate) = YEAR(CURDATE()) 
                 AND MONTH(StartDate) = MONTH(CURDATE())";
$loans = fetchData($queryLoans, [':user_id' => $userID], $conn);
if ($loans !== false && $loans['TotalLoans'] !== null) {
    $totalLoans = $loans['TotalLoans'];
}

// Fetch the user's income from the savings table
$queryIncome = "SELECT TotalIncome 
                FROM savings 
                WHERE UserID = :user_id 
                  AND YEAR(DateCreated) = YEAR(CURDATE()) 
                  AND MONTH(DateCreated) = MONTH(CURDATE())";
$income = fetchData($queryIncome, [':user_id' => $userID], $conn);
if ($income !== false && $income['TotalIncome'] !== null) {
    $totalIncome = $income['TotalIncome'];
}

// Calculate savings
$totalSavings = $totalIncome - ($totalExpenses + $totalInvestments + $totalLoans);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Savings Overview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Navigation Bar */
        nav {
        background-color: #227474;
        padding: 10px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        }

        nav ul {
        list-style: none;
        display: flex;
        justify-content: center;
        gap: 20px;
        }

        nav ul li {
        display: inline;
        }

        nav ul li a {
        text-decoration: none;
        color: #ffffff;
        font-weight: bold;
        transition: color 0.3s ease;
        }

        nav ul li a:hover {
        color: #ffce56;
        }
        h1 {
            color: #227474;
            margin-bottom: 20px;
            text-align: center;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .overview {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .overview p {
            font-size: 18px;
            margin: 10px 0;
            color: #555;
        }
        .overview p span {
            font-weight: bold;
            color: #333;
        }
        .suggestions {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .suggestions p {
            font-size: 16px;
            color: #444;
            text-align: center;
            font-weight: bold;
        }
        .negative-suggestion {
            color: #e74c3c;
        }
        .positive-suggestion {
            color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Savings, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>

    <!-- Navigation Bar (optional) -->
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="expenses.php">Daily Expense Trends</a></li>
            <li><a href="generate_insights.php">Saving Insights</a></li>
            <li><a href="check_badges.php">Achievements</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <h2>Monthly Savings Overview</h2>
    
    <div class="overview">
        <p>Total Income: <span>₹<?php echo number_format($totalIncome, 2); ?></span></p>
        <p>Total Expenses: <span>₹<?php echo number_format($totalExpenses, 2); ?></span></p>
        <p>Total Investments: <span>₹<?php echo number_format($totalInvestments, 2); ?></span></p>
        <p>Total Loan Repayments: <span>₹<?php echo number_format($totalLoans, 2); ?></span></p>
        <p>Total Savings for the Month: <span>₹<?php echo number_format($totalSavings, 2); ?></span></p>
    </div>

    <div class="suggestions">
        <h3>Suggestions for Improving Savings:</h3>
        <?php if ($totalSavings < 0): ?>
            <p class="negative-suggestion">Your expenses, investments, and loans are higher than your income this month. Try to cut back on non-essential spending!</p>
        <?php else: ?>
            <p class="positive-suggestion">You're saving well this month! Consider increasing your investments for long-term growth.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>




