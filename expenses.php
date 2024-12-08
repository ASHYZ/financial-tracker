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

// Handle expense form submission (for Expense, Investment, Loan)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];  // Expense, Investment, Loan
    $amount = $_POST['amount'];
    $description = $_POST['description'];  // Optional description
    $date = date('Y-m-d'); // Today's date

    // Determine table to insert based on category
    if ($category == 'Expense') {
        $query = "INSERT INTO expenses (UserID, Category, Amount, Description, ExpenseDate) 
                  VALUES (:user_id, :category, :amount, :description, :date)";
    } elseif ($category == 'Investment') {
        $query = "INSERT INTO investments (UserID, Type, Amount, InvestmentDate) 
                  VALUES (:user_id, :category, :amount, :date)";
    } elseif ($category == 'Loan') {
        $query = "INSERT INTO loans (UserID, LoanType, PrincipalAmount, EMIAmount, InterestRate, StartDate) 
                  VALUES (:user_id, :category, :amount, :amount, 5.0, :date)";  // Example loan details
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userID);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker</title>
    <style>
        /* Base styling */
        body {
            font-family: 'Georgia', serif;
            background-color: #f8f4ec;
            color: #2f2d2c;
            margin: 0;
            padding: 0;
        }

        /* Container for the form */
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fffdf5;
            border: 1px solid #d9cbbf;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
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

        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #2a2725;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            font-weight: bold;
            font-size: 1rem;
        }

        input, select, button {
            margin-top: 5px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #b3a7a0;
            border-radius: 5px;
            width: 100%;
        }

        input:focus, select:focus, button:focus {
            outline: none;
            border-color: #857a72;
            box-shadow: 0px 0px 5px rgba(133, 122, 114, 0.6);
        }

        button {
            margin-top: 20px;
            background-color: #857a72;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #6e635c;
        }

        /* Subtle aesthetics */
        .container::before {
            content: "Finance Tracker";
            display: block;
            font-family: 'Georgia', serif;
            font-size: 0.8rem;
            text-align: center;
            color: #b3a7a0;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            h1 {
                font-size: 1.8rem;
            }

            input, select, button {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
   <h1>Expenses, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <!-- Navigation Bar (optional) -->
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="track_expenses.php">Daily Expense Trends</a></li>
                    <li><a href="generate_insights.php">Saving Insights</a></li>
                    <li><a href="check_badges.php">Achievements</a></li>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
    <div class="container">

        <h2>Log Financial Entry</h1>
        <form method="POST">
            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="Expense">Expense</option>
                <option value="Investment">Investment</option>
                <option value="Loan">Loan</option>
            </select>
            
            <label for="amount">Amount (₹):</label>
            <input type="number" name="amount" id="amount" required>
            
            <label for="description">Description:</label>
            <input type="text" name="description" id="description">
            
            <button type="submit">Log Entry</button>
        </form>
    </div>
</body>
</html>

