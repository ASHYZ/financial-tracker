<?php
header('Content-Type: application/json');
include 'db_config.php';

session_start();

$query = "SELECT DAYNAME(ExpenseDate) AS day, SUM(Amount) AS amount FROM expenses WHERE UserID = :user_id GROUP BY DAYNAME(ExpenseDate)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>
