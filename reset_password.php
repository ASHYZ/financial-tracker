<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "SELECT * FROM users WHERE reset_token = :token";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $query = "UPDATE users SET password = :password, reset_token = NULL WHERE reset_token = :token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':password', $new_password);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        echo "<p>Password updated successfully. <a href='login.php'>Login</a></p>";
    } else {
        echo "<p>Invalid or expired token.</p>";
    }
} else {
    $token = $_GET['token'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
</head>
<body>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="password">New Password</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
