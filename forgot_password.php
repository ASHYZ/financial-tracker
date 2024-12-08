<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_token = bin2hex(random_bytes(32)); // Generate a secure token
        $query = "UPDATE users SET reset_token = :reset_token WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':reset_token', $reset_token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Send reset link via email
        $reset_link = "http://localhost/myproject-Financial_Tracker/reset_password.php?token=$reset_token";
        mail($email, "Password Reset", "Click this link to reset your password: $reset_link");

        echo "<p>Reset link sent to your email.</p>";
    } else {
        echo "<p>Email not found.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
</head>
<body>
    <form method="POST">
        <label for="email">Enter your email</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
