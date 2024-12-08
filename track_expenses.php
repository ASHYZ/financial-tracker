<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db_config.php';

$userId = $_SESSION['user_id'];

// Weekly Expenditure Data
$weekly_query = "SELECT DATE(ExpenseDate) AS date, SUM(Amount) AS total 
                 FROM expenses 
                 WHERE UserID = :userId 
                 AND ExpenseDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                 GROUP BY DATE(ExpenseDate) 
                 ORDER BY DATE(ExpenseDate)";
$stmt = $conn->prepare($weekly_query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$weekly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly Expenditure Data
$monthly_query = "SELECT DATE(ExpenseDate) AS date, SUM(Amount) AS total 
                  FROM expenses 
                  WHERE UserID = :userId 
                  AND MONTH(ExpenseDate) = MONTH(CURDATE()) 
                  AND YEAR(ExpenseDate) = YEAR(CURDATE()) 
                  GROUP BY DATE(ExpenseDate) 
                  ORDER BY DATE(ExpenseDate)";
$stmt = $conn->prepare($monthly_query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenditure Trends</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    line-height: 1.6;
    background: linear-gradient(to right, #ffffff, #f8f9fa), 
                repeating-linear-gradient(
                    0deg,
                    #e0e0e0 0px,
                    #e0e0e0 1px,
                    transparent 1px,
                    transparent 20px
                ),
                repeating-linear-gradient(
                    90deg,
                    #e0e0e0 0px,
                    #e0e0e0 1px,
                    transparent 1px,
                    transparent 20px
                );
    background-size: 20px 20px, 20px 20px;
    color: #2c3e50;
}

h1 {
    text-align: center;
    margin-top: 20px;
    color: #34495e;
    font-size: 3rem;
    font-weight: 700;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
}

.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
}

h2 {
    text-align: center;
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 20px;
}


    canvas {
        display: block;
        margin: 0 auto;
        max-width: 800px;
        padding: 20px 0;
    }

    footer {
    text-align: center;
    background: #2c3e50;
    color: white;
    padding: 15px;
    font-size: 0.9rem;
}

footer p {
    margin: 0;
}

footer a {
    color: #f1c40f;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}


    .cta-button {
    display: block;
    margin: 20px auto;
    padding: 12px 25px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #ffffff;
    background: linear-gradient(to right, #002D62,  #5a4f4e);
    border: none;
    border-radius: 25px;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.cta-button:hover {
    background: linear-gradient(to right,  #5a4f4e,#002D62);
    transform: scale(1.05);
}

</style>

</head>
<body>
    <h1><i class="fas fa-chart-line"></i> Expenditure Trends</h1>

    <div class="container">
        <h2>Weekly Trends</h2>
        <canvas id="weeklyChart"></canvas>
        <a href="detailed_report.php" class="cta-button">View Weekly Report</a>
    </div>

    <div class="container">
        <h2>Monthly Trends</h2>
        <canvas id="monthlyChart"></canvas>
        <a href="detailed_report.php" class="cta-button">View Monthly Report</a>
    </div>

    <footer>
        <p>“An investment in knowledge pays the best interest.” – Benjamin Franklin</p>
        <p>&copy; 2024 Finance Manager. Designed with 💰 by <a href="about_us.html">Your Team</a>.</p>
    </footer>
</body>


    <script>
    // Weekly Data from PHP
    const weeklyLabels = <?php echo json_encode(array_column($weekly_data, 'date')); ?>;
    const weeklyTotals = <?php echo json_encode(array_column($weekly_data, 'total')); ?>;

    // Monthly Data from PHP
    const monthlyLabels = <?php echo json_encode(array_column($monthly_data, 'date')); ?>;
    const monthlyTotals = <?php echo json_encode(array_column($monthly_data, 'total')); ?>;

    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Weekly Expenditure',
                data: weeklyTotals,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: '#3498db',
                borderWidth: 2,
                hoverBackgroundColor: 'rgba(41, 128, 185, 0.9)',
                hoverBorderColor: '#2980b9',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: '#34495e' }
                },
                tooltip: { enabled: true }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#2c3e50' } },
                y: { ticks: { color: '#2c3e50' } }
            }
        }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Monthly Expenditure',
                data: monthlyTotals,
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.2)',
                borderWidth: 2,
                pointStyle: 'circle',
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: '#e74c3c' }
                },
                tooltip: { enabled: true }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#2c3e50' } },
                y: { ticks: { color: '#2c3e50' } }
            }
        }
    });
</script>
</html>
