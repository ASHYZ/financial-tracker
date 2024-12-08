<?php
include 'db_config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // Fetch tax history for the logged-in user
    $query = "SELECT * FROM tax_calculations WHERE UserID = :user_id ORDER BY year DESC, month DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $taxHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for Chart.js
    $labels = [];
    $grossSalaries = [];
    $taxes = [];
    
    // Handle tax history data for chart
    foreach ($taxHistory as $row) {
        // Format date to 'Month Year'
        $labels[] = date("F Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
        // Append gross salary and tax payable to the respective arrays
        $grossSalaries[] = $row['gross_salary'];
        $taxes[] = $row['total_tax_payable'];
    }
} catch (Exception $e) {
    // Handle database errors
    $taxHistory = [];
    $errorMessage = "Unable to fetch tax history. Please try again later.";
}
// Get total taxable income and tax payable from the session
$totalTaxableIncome = isset($_SESSION['total_taxable_income']) ? $_SESSION['total_taxable_income'] : 0;
$totalTaxPayable = isset($_SESSION['total_tax_payable']) ? $_SESSION['total_tax_payable'] : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax History</title>
    
    <style>
        /* Styling for better presentation */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(90deg, #2b7a68, #1864d7);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding-bottom: 10px;
        }

        .navbar .logo {
            color: #fff;
            font-size: 1.5em;
            font-weight: bold;
        }
        .nav-links {
            list-style: none;
            margin: 0;
            padding-right: 40px;
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1em;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: linear-gradient(90deg, #2b7a68, #1864d7);
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .error-message {
            color: red;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">Finance Tracker</div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="tax.php">Tax Calculator</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Tax History</h1>

        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <table>
            <tr>
                <th>Date</th>
                <th>Gross Salary (₹)</th>
                <th>Total Taxable Income (₹)</th>
                <th>Total Tax Payable (₹)</th>
            </tr>
            <?php if (!empty($taxHistory)): ?>
                <?php foreach ($taxHistory as $row): ?>
                    <tr>
                        <td>
                            <?php
                            // Format the date with month name
                            echo htmlspecialchars(date("F Y", mktime(0, 0, 0, $row['month'], 1, $row['year'])));
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(number_format($row['gross_salary'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['total_taxable_income'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['total_tax_payable'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No tax history found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Graphical Analysis Section -->
        <h2>Graphical Analysis</h2>
        <canvas id="taxChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for Chart.js
        const labels = <?php echo json_encode($labels); ?>; // X-axis labels (Months)
        const grossSalaries = <?php echo json_encode($grossSalaries); ?>; // Gross Salaries
        const taxes = <?php echo json_encode($taxes); ?>; // Taxes

        // Render the chart
        const ctx = document.getElementById('taxChart').getContext('2d');
        const taxChart = new Chart(ctx, {
            type: 'line', // Use a line chart
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Gross Salary (₹)',
                        data: grossSalaries,
                        borderColor: '#2b7a68',
                        backgroundColor: 'rgba(43, 122, 104, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Tax Payable (₹)',
                        data: taxes,
                        borderColor: '#f54748',
                        backgroundColor: 'rgba(245, 71, 72, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    </script>
</body>
</html>






