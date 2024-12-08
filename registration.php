<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "<p>Account created successfully! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p>Failed to create account. Try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .terms {
            font-size: 12px;
            color: #555555;
            margin-top: 10px;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Logo Section -->
        <img src="images\ftlogo.png" alt="Website Logo" class="logo">

        <h1>Create Account</h1>
        <form method="POST">
            <label for="name">Your name</label>
            <input type="text" name="name" id="name" placeholder="Enter your name" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="At least 6 characters" required>
            <button type="submit">Create your account</button>
        </form>
        <div class="terms">
            By creating an account, you agree to our <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>.
        </div>
        <div class="additional-links">
            <p>Already have an account? <a href="login.php">Sign-In</a></p>
        </div>
    </div>
</body>
</html>
