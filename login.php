<?php
include 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (!empty($email) && !empty($password)) {
        try {
            // Prepare query securely
            $query = "SELECT * FROM users WHERE Email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Fetch user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debugging step: Verify user data is retrieved
            if ($user === false) {
                $error_message = "No user found with the provided email.";
            } else if (isset($user['password']) && password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Store user information in session
                $_SESSION['user_id'] = $user['UserID']; // Assuming column name is UserID
                $_SESSION['username'] = $user['name']; // Assuming column name is Name

                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            // Catch database errors and log them
            error_log("Database error: " . $e->getMessage());
            $error_message = "An error occurred. Please try again later.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
        }
        .form-container {
            max-width: 350px;
            margin: 5% auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .form-container img.logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .form-container h1 {
            font-size: 24px;
            font-weight: 500;
            color: #333333;
            margin-bottom: 20px;
        }
        .form-container label {
            font-size: 14px;
            font-weight: 500;
            color: #555555;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            margin-bottom: 16px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            font-weight: 500;
            background-color: #f0c14b;
            border: 1px solid #a88734;
            border-radius: 4px;
            cursor: pointer;
            color: #111111;
        }
        .form-container button:hover {
            background-color: #e2b03a;
        }
        .error {
            color: #d93025;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .additional-links {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
        }
        .additional-links a {
            color: #007185;
            text-decoration: none;
        }
        .additional-links a:hover {
            text-decoration: underline;
        }
        .terms {
            font-size: 12px;
            color: #555555;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img src="images/ftlogo.png" alt="Website Logo" class="logo">
        <h1>Sign-In</h1>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <button type="submit">Sign-In</button>
        </form>
        <div class="additional-links">
            <p>New to this site? <a href="registration.php">Create your account</a></p>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
        </div>
        <div class="terms">
            By signing in, you agree to our <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>.
        </div>
    </div>
</body>
</html>
