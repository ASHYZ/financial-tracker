<?php
session_start();
include('db_config.php'); // Include database connection

// Initialize variables for displaying the calculated tax
$calculated_tax = 0;

// Get the current year and month
$year = date("Y"); // Current year
$month = date("m"); // Current month

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $gross_salary = floatval($_POST['gross_salary']);
    $other_income = floatval($_POST['other_income']);
    $age_category = $_POST['age_category'];

    // Calculate taxable income
    $taxable_income = $gross_salary + $other_income;

    // Tax calculation logic for below 60 category
    if ($age_category == 'below60') {
        if ($gross_salary <= 250000) {
            $calculated_tax = 0;
        } elseif ($gross_salary <= 500000) {
            $calculated_tax = ($gross_salary - 250000) * 0.05;
        } elseif ($gross_salary <= 1000000) {
            $calculated_tax = ($gross_salary - 500000) * 0.1 + 12500;
        } else {
            $calculated_tax = ($gross_salary - 1000000) * 0.2 + 12500 + 50000;
        }
    }

    // Tax calculation logic for 60 to 80 age category (Senior Citizens)
    elseif ($age_category == '60to80') {
        if ($gross_salary <= 300000) {
            $calculated_tax = 0;  // Senior citizens get an exemption for income up to ₹300,000
        } elseif ($gross_salary <= 500000) {
            $calculated_tax = ($gross_salary - 300000) * 0.05;
        } elseif ($gross_salary <= 1000000) {
            $calculated_tax = ($gross_salary - 500000) * 0.1 + 10000;
        } else {
            $calculated_tax = ($gross_salary - 1000000) * 0.2 + 10000 + 50000;
        }
    }

    // Tax calculation logic for above 80 age category (Super Senior Citizens)
    elseif ($age_category == 'above80') {
        if ($gross_salary <= 500000) {
            $calculated_tax = 0;  // Super senior citizens get an exemption for income up to ₹500,000
        } elseif ($gross_salary <= 1000000) {
            $calculated_tax = ($gross_salary - 500000) * 0.05;
        } elseif ($gross_salary <= 2000000) {
            $calculated_tax = ($gross_salary - 1000000) * 0.1 + 25000;
        } else {
            $calculated_tax = ($gross_salary - 2000000) * 0.2 + 25000 + 100000;
        }
    }

    // Add tax on other income (assuming 10%)
    $calculated_tax += $other_income * 0.1;

    // Store the taxable income and calculated tax in session variables
    $_SESSION['total_taxable_income'] = $taxable_income;
    $_SESSION['total_tax_payable'] = $calculated_tax;

    // Save tax to session and database when "Save and Continue" is clicked
    if (isset($_POST['save_tax']) && $_POST['save_tax'] == 'yes') {
        $_SESSION['saved_tax'] = $calculated_tax;

        // Database Insertion (using PDO)
        $query = "INSERT INTO tax_calculations(age_category, gross_salary, other_income, taxable_income, tax_payable, UserID, year, month)
                  VALUES (:age_category, :gross_salary, :other_income, :taxable_income, :tax_payable, :user_id, :year, :month)";
        
        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind parameters using bindValue()
        $stmt->bindValue(':age_category', $age_category, PDO::PARAM_STR);
        $stmt->bindValue(':gross_salary', $gross_salary, PDO::PARAM_STR);
        $stmt->bindValue(':other_income', $other_income, PDO::PARAM_STR);
        $stmt->bindValue(':taxable_income', $taxable_income, PDO::PARAM_STR);
        $stmt->bindValue(':tax_payable', $calculated_tax, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':month', $month, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to tax_graph page after saving the tax
            header("Location: tax_graph.php");
            exit(); // Make sure to call exit() after header to stop further execution
        } else {
            echo "Error: " . $stmt->errorInfo();
        }

        // Database Insertion for `tax_graph` table (using PDO)
        $query = "INSERT INTO tax_graph(age_category, total_taxable_income, total_tax_payable, month, year, UserID)
                  VALUES (:age_category, :total_taxable_income, :total_tax_payable, :month, :year, :user_id)";
        
        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind parameters using bindValue()
        $stmt->bindValue(':age_category', $age_category, PDO::PARAM_STR);
        $stmt->bindValue(':total_taxable_income', $taxable_income, PDO::PARAM_STR);
        $stmt->bindValue(':total_tax_payable', $calculated_tax, PDO::PARAM_STR);
        $stmt->bindValue(':month', $month, PDO::PARAM_INT);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->errorInfo();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Calculation</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Function to confirm saving the tax and redirecting
        function confirmSaveTax() {
            if (confirm("Do you want to save the calculated tax?")) {
                document.getElementById('save_tax').value = 'yes';
                document.getElementById('tax_form').submit();
            }
        }
    </script>
</head>
<body>

    <header>
        <h1>Tax Calculator</h1>
    </header>

    <div class="container">
        <form id="tax_form" method="POST" action="">
            <!-- Age Category Dropdown -->
            <label for="age_category">Select Age Category:</label>
            <select name="age_category" id="age_category">
                <option value="below60" <?php echo isset($_POST['age_category']) && $_POST['age_category'] == 'below60' ? 'selected' : ''; ?>>Below 60</option>
                <option value="60to80" <?php echo isset($_POST['age_category']) && $_POST['age_category'] == '60to80' ? 'selected' : ''; ?>>60 to 80 (Senior Citizen)</option>
                <option value="above80" <?php echo isset($_POST['age_category']) && $_POST['age_category'] == 'above80' ? 'selected' : ''; ?>>Above 80 (Super Senior Citizen)</option>
            </select>

            <br><br>

            <!-- Gross Salary Dropdown -->
            <label for="gross_salary">Enter Gross Salary:</label>
            <select name="gross_salary" id="gross_salary">
                <option value="300000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '300000' ? 'selected' : ''; ?>>₹3,00,000</option>
                <option value="600000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '600000' ? 'selected' : ''; ?>>₹6,00,000</option>
                <option value="900000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '900000' ? 'selected' : ''; ?>>₹9,00,000</option>
                <option value="1200000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '1200000' ? 'selected' : ''; ?>>₹12,00,000</option>
                <option value="1500000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '1500000' ? 'selected' : ''; ?>>₹15,00,000</option>
                <option value="2000000" <?php echo isset($_POST['gross_salary']) && $_POST['gross_salary'] == '2000000' ? 'selected' : ''; ?>>₹20,00,000</option>
            </select>

            <br><br>

            <!-- Other Income Dropdown -->
            <label for="other_income">Enter Other Income:</label>
            <select name="other_income" id="other_income">
                <option value="0" <?php echo isset($_POST['other_income']) && $_POST['other_income'] == '0' ? 'selected' : ''; ?>>₹0</option>
                <option value="50000" <?php echo isset($_POST['other_income']) && $_POST['other_income'] == '50000' ? 'selected' : ''; ?>>₹50,000</option>
                <option value="100000" <?php echo isset($_POST['other_income']) && $_POST['other_income'] == '100000' ? 'selected' : ''; ?>>₹1,00,000</option>
                <option value="200000" <?php echo isset($_POST['other_income']) && $_POST['other_income'] == '200000' ? 'selected' : ''; ?>>₹2,00,000</option>
            </select>

            <br><br>

            <!-- Display calculated tax -->
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <p><strong>Calculated Tax: ₹<?php echo number_format($calculated_tax, 2); ?></strong></p>
                <button type="button" onclick="confirmSaveTax()">Save and Continue</button>
            <?php else: ?>
                <button type="submit">Calculate Tax</button>
            <?php endif; ?>
            <!-- Hidden input to flag saving tax -->
            <input type="hidden" id="save_tax" name="save_tax" value="">
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Tax Calculator</p>
    </footer>

</body>
</html>
