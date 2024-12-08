<?php
session_start();

// Include database configuration
include_once 'db_config.php';

// Check if the user is logged in
$userID = $_SESSION['user_id'] ?? null;
if (!$userID) {
    die("User not logged in. Please log in to continue.");
}

// Fetch total income, expenses, loans, and investments for the user
$queryIncome = "SELECT TotalIncome FROM savings WHERE UserID = :user_id AND YEAR(DateCreated) = YEAR(CURDATE()) AND MONTH(DateCreated) = MONTH(CURDATE())";
$stmtIncome = $conn->prepare($queryIncome);
if (!$stmtIncome) {
    die("Error preparing income query: " . print_r($conn->errorInfo(), true));
}
$stmtIncome->bindParam(':user_id', $userID);
$stmtIncome->execute();
$income = $stmtIncome->fetch(PDO::FETCH_ASSOC);
$totalIncome = $income ? $income['TotalIncome'] : 0;

// Fetch total expenses for the current month
$queryExpenses = "SELECT SUM(Amount) AS TotalExpenses FROM expenses WHERE UserID = :user_id AND YEAR(ExpenseDate) = YEAR(CURDATE()) AND MONTH(ExpenseDate) = MONTH(CURDATE())";
$stmtExpenses = $conn->prepare($queryExpenses);
if (!$stmtExpenses) {
    die("Error preparing expenses query: " . print_r($conn->errorInfo(), true));
}
$stmtExpenses->bindParam(':user_id', $userID);
$stmtExpenses->execute();
$expenses = $stmtExpenses->fetch(PDO::FETCH_ASSOC);
$totalExpenses = $expenses ? $expenses['TotalExpenses'] : 0;

$queryInvestments = "SELECT SUM(Amount) AS TotalInvestments FROM investments WHERE UserID = :user_id AND YEAR(InvestmentDate) = YEAR(CURDATE()) AND MONTH(InvestmentDate) = MONTH(CURDATE())";
$stmtInvestments = $conn->prepare($queryInvestments);
if (!$stmtInvestments) {
    die("Error preparing investments query: " . print_r($conn->errorInfo(), true));
}
$stmtInvestments->bindParam(':user_id', $userID);
$stmtInvestments->execute();
$investments = $stmtInvestments->fetch(PDO::FETCH_ASSOC);
$totalInvestments = $investments ? $investments['TotalInvestments'] : 0;

$queryLoans = "SELECT SUM(EMIAmount) AS TotalLoans FROM loans WHERE UserID = :user_id AND YEAR(StartDate) = YEAR(CURDATE()) AND MONTH(StartDate) = MONTH(CURDATE())";
$stmtLoans = $conn->prepare($queryLoans);
if (!$stmtLoans) {
    die("Error preparing loans query: " . print_r($conn->errorInfo(), true));
}
$stmtLoans->bindParam(':user_id', $userID);
$stmtLoans->execute();
$loans = $stmtLoans->fetch(PDO::FETCH_ASSOC);
$totalLoans = $loans ? $loans['TotalLoans'] : 0;

// Calculate savings
$totalSavings = $totalIncome - ($totalExpenses + $totalInvestments + $totalLoans);

// Dynamic insight generation based on financial situation
$insightText = '';

// Check for high loan payments
if ($totalLoans > $totalIncome * 0.3) {
    $insightText .= "Your loan repayments are quite high compared to your income. Consider refinancing your loans or focusing on high-interest loans first to reduce the burden. ";
}

// Check for excessive spending on non-essentials
if ($totalExpenses > $totalIncome * 0.5) {
    $insightText .= "Your expenses are too high this month. Look at reducing discretionary spending such as dining out, entertainment, and shopping. ";
}

// Suggest investing if savings are high
if ($totalSavings > $totalIncome * 0.2) {
    $insightText .= "You are saving well this month! Consider increasing your investments in long-term growth assets such as stocks or mutual funds to maximize your returns. ";
}

// Suggest cutting down on unnecessary investments if savings are low
if ($totalSavings < 0 && $totalInvestments > $totalIncome * 0.1) {
    $insightText .= "You might want to cut back on non-essential investments for now. Focus on building your savings before making more investments. ";
}

// If the user has children fees, suggest managing them
$queryChildren = "SELECT SUM(Amount) AS TotalChildrenFees FROM expenses WHERE UserID = :user_id AND Category = 'Children' AND YEAR(ExpenseDate) = YEAR(CURDATE()) AND MONTH(ExpenseDate) = MONTH(CURDATE())";
$stmtChildren = $conn->prepare($queryChildren);
if (!$stmtChildren) {
    die("Error preparing children fees query: " . print_r($conn->errorInfo(), true));
}
$stmtChildren->bindParam(':user_id', $userID);
$stmtChildren->execute();
$childrenFees = $stmtChildren->fetch(PDO::FETCH_ASSOC);
$totalChildrenFees = $childrenFees ? $childrenFees['TotalChildrenFees'] : 0;

if ($totalChildrenFees > 0) {
    $insightText .= "Consider setting aside a fixed amount each month for your children's education fees to avoid last-minute financial strain. ";
}

// Insert the dynamic insight into the database
$queryInsight = "INSERT INTO insights (UserID, InsightText, DateCreated) VALUES (:user_id, :insight_text, CURDATE())";
$stmtInsight = $conn->prepare($queryInsight);
if (!$stmtInsight) {
    die("Error preparing insight insertion query: " . print_r($conn->errorInfo(), true));
}

$stmtInsight->bindParam(':user_id', $userID);
$stmtInsight->bindParam(':insight_text', $insightText);

if (!$stmtInsight->execute()) {
    die("Error executing insight insertion query: " . print_r($stmtInsight->errorInfo(), true));
}

// Display the insight
echo $insightText;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Insights</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: "Georgia", serif;
        background-color: #f4f1e1;
        color: #4b4b4b;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
/* Navigation Bar */
   nav {
        background-color: #2a2725;
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
        color: #2a2714;
        }
        h1 {
            color: #2a2725;
            margin-bottom: 20px;
            text-align: center;
        }

    .header {
        text-align: center;
        font-size: 36px;
        color: #2e2b23;
        margin-bottom: 30px;
    }

    .insight-text {
        font-size: 18px;
        line-height: 1.6;
        text-align: justify;
        margin-bottom: 30px;
        color: #5a4f4e;
    }

    footer {
        text-align: center;
        font-size: 14px;
        color: #ffff;
        margin-top: 50px;
    }

    button {
        background-color: #8a7f66;
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #726b56;
    }

    a {
        color: #8a7f66;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        color: #5a4f4e;
    }

    body {
        padding: 10px;
    }
    </style>
</head>
<body>
      <h1>Insights for you, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
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
    <div class="container">
        <div class="header">
            <h2>Your Financial Insights</h1>
        </div>

        <div class="insight-text">
            <?php
                echo $insightText;
            ?>
        </div>

        <footer>
            <p>Powered by Your Financial Tracker</p>
        </footer>
    </div>

</body>
</html>
