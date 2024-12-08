<?php 
    session_start(); 
    if (!isset($_SESSION['user_id'])) {     
        header('Location: login.php'); // Redirect to login if not logged in     
        exit(); 
    }  
    include 'db_config.php'; 

    $userId = $_SESSION['user_id']; 

    // Total Savings
    $savings_query = "SELECT SUM(TotalSavings) AS total_savings 
                      FROM savings 
                      WHERE UserID = :userId AND Year = YEAR(CURDATE())";
    $stmt = $conn->prepare($savings_query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $total_savings = $stmt->fetchColumn();

    // Total Expenses
    $expenses_query = "SELECT SUM(Amount) AS total_expenses 
                       FROM expenses 
                       WHERE UserID = :userId AND Category = 'Expense' AND YEAR(ExpenseDate) = YEAR(CURDATE())";
    $stmt = $conn->prepare($expenses_query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $total_expenses = $stmt->fetchColumn();

    // Tax Calculations
    $tax_query = "SELECT total_tax_payable 
                  FROM tax_calculations 
                  WHERE UserID = :userId AND Year = YEAR(CURDATE())";
    $stmt = $conn->prepare($tax_query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $tax_payable = $stmt->fetchColumn();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Manager Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fffdf5;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }

        /* Header Styles */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        header .logo {
            font-size: 2rem;
            font-weight: bold;
        }

        nav {
            display: flex;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #5a4f4e;
            margin: 0 15px;
            font-size: 1.1rem;
            font-weight: bold;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #5a4f22;
        }

        /* Dashboard Hero Section */
        .hero {
            text-align: center;
            padding: 50px 20px;
            background: #f8f9fa;
        }

        .hero h1 {
            font-size: 2.5rem;
            color: #000435;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 10px;
            color: #2c3e50;
        }

        /* Dashboard Cards Section */
        .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 30px auto;
            max-width: 1200px;
        }

        .card {
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin: 15px;
            width: 280px;
            text-align: center;
        }

        .card h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1rem;
            color: #7f8c8d;
        }

        .card .cta-button {
            display: inline-block;
            margin-top: 15px;
            background-color: #000435;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .card .cta-button:hover {
            background-color: #002D7F;
        }

        /* Footer */
        footer {
            background-color: #5a4f4e;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <div class="logo">
        <img src="images/ftlogo.png" alt="Finance Tracker Logo" style="height: 50px; width: auto;">
    </div>
    <nav>
        <div class="nav-links">
            <a href="check_badges.php">Achievements</a>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<!-- Hero Section -->
<section class="hero">
    <h1>Welcome to Your Dashboard</h1>
    <p>Track your financial health at a glance. Stay on top of your expenses, savings, and more.</p>
</section>

<!-- Dynamic Dashboard -->
<section class="cards">
    <div class="card">
        <h2>Total Savings</h2>
        <p>₹<?php echo number_format($total_savings, 2); ?></p>
        <a href="savings.php" class="cta-button">View Details</a>
    </div>
    <div class="card">
        <h2>Total Expenses</h2>
        <p>₹<?php echo number_format($total_expenses, 2); ?></p>
        <a href="expenses.php" class="cta-button">Log Expenses</a>
    </div>
    <div class="card">
        <h2>Tax History</h2>
        <p>₹<?php echo number_format($tax_payable, 2); ?> Pending</p>
        <a href="tax_graph.php" class="cta-button">Calculate Tax</a>
    </div>
    <div class="card">
        <h2>Financial Reports</h2>
        <p>Detailed Analysis of Your Finances</p>
        <a href="generate_insights.php" class="cta-button">View Reports</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Finance Manager. All rights reserved.</p>
</footer>

</body>
</html>
