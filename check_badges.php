<?php
include 'db_config.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// --- Savings Streak Badge: "Savings Champion" ---
$savingsStreakQuery = "
    SELECT COUNT(*) AS streak
    FROM expenses 
    WHERE UserID = :user_id 
      AND Category = 'Savings' 
      AND ExpenseDate >= CURDATE() - INTERVAL 7 DAY";
$savingsStreakStmt = $conn->prepare($savingsStreakQuery);
$savingsStreakStmt->bindParam(':user_id', $userId);
$savingsStreakStmt->execute();
$savingsStreak = $savingsStreakStmt->fetch(PDO::FETCH_ASSOC)['streak'] ?? 0;

// Check if badge already exists
$badgeCheckQuery = "
    SELECT COUNT(*) AS badge_exists
    FROM achievementbadges 
    WHERE UserID = :user_id 
      AND BadgeName = 'Savings Champion'";
$badgeCheckStmt = $conn->prepare($badgeCheckQuery);
$badgeCheckStmt->bindParam(':user_id', $userId);
$badgeCheckStmt->execute();
$badgeExists = $badgeCheckStmt->fetch(PDO::FETCH_ASSOC)['badge_exists'] ?? 0;

// Award "Savings Champion" badge if streak is 4 or more and not already awarded
if ($savingsStreak >= 4 && $badgeExists == 0) {
    $awardBadgeQuery = "
        INSERT INTO achievementbadges (UserID, BadgeName, DateAchieved) 
        VALUES (:user_id, 'Savings Champion', CURDATE())";
    $awardBadgeStmt = $conn->prepare($awardBadgeQuery);
    $awardBadgeStmt->bindParam(':user_id', $userId);
    $awardBadgeStmt->execute();
}

// --- Monthly Savings Badge: "Savings Expert" ---
$savingsQuery = "
    SELECT TotalSavings 
    FROM savings 
    WHERE UserID = :user_id 
      AND Month = MONTH(CURDATE()) 
      AND Year = YEAR(CURDATE())";
$savingsStmt = $conn->prepare($savingsQuery);
$savingsStmt->bindParam(':user_id', $userId);
$savingsStmt->execute();
$totalSavings = $savingsStmt->fetch(PDO::FETCH_ASSOC)['TotalSavings'] ?? 0;

// Check if badge already exists
$badgeCheckQuery = "
    SELECT COUNT(*) AS badge_exists
    FROM achievementbadges 
    WHERE UserID = :user_id 
      AND BadgeName = 'Savings Expert'";
$badgeCheckStmt = $conn->prepare($badgeCheckQuery);
$badgeCheckStmt->bindParam(':user_id', $userId);
$badgeCheckStmt->execute();
$badgeExists = $badgeCheckStmt->fetch(PDO::FETCH_ASSOC)['badge_exists'] ?? 0;

// Award "Savings Expert" badge if monthly savings exceed 20,000 and not already awarded
if ($totalSavings >= 20000 && $badgeExists == 0) {
    $awardBadgeQuery = "
        INSERT INTO achievementbadges (UserID, BadgeName, DateAchieved) 
        VALUES (:user_id, 'Savings Expert', CURDATE())";
    $awardBadgeStmt = $conn->prepare($awardBadgeQuery);
    $awardBadgeStmt->bindParam(':user_id', $userId);
    $awardBadgeStmt->execute();
}

// --- Fetch All Badges ---
$allBadgesQuery = "
    SELECT BadgeName, DateAchieved
    FROM achievementbadges  
    WHERE UserID = :user_id 
    ORDER BY DateAchieved DESC";
$allBadgesStmt = $conn->prepare($allBadgesQuery);
$allBadgesStmt->bindParam(':user_id', $userId);
$allBadgesStmt->execute();
$badges = $allBadgesStmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

// Prepare data for response
$response = [
    'current_streak' => $savingsStreak,
    'monthly_savings' => $totalSavings,
    'badges' => $badges
];

// Output JSON if requested
if (isset($_GET['json_response']) && $_GET['json_response'] === 'true') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements</title>
    <style>
      /* Custom Styles for Achievements */
      body {
          font-family: 'Arial', sans-serif;
          background: #f4f7fb;
          margin: 0;
          padding: 0;
      }

      /* Container for the content */
      .container {
          max-width: 900px;
          margin: 20px auto;
          background: #ffffff;
          border-radius: 12px;
          padding: 30px;
          box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
          font-family: 'Arial', sans-serif;
          background: linear-gradient(135deg, #f6d365, #fda085); /* Adding gradient background */
      }

      /* Heading Style */
      h1 {
          color: #2e3b47;
          text-align: center;
          font-size: 32px;
          margin-bottom: 30px;
          font-weight: bold;
      }

      /* Achievement Status Section */
      .achievement-status {
          margin: 30px 0;
      }

      .achievement-status h2 {
          color: #4caf50;
          font-size: 24px;
          margin-bottom: 10px;
      }

      /* Badge Progress Bar */
      .badge-progress {
          display: flex;
          align-items: center;
          gap: 15px;
          margin: 20px 0;
      }

      .badge-progress-bar {
          flex: 1;
          height: 30px;
          background: #e0e0e0;
          border-radius: 15px;
          position: relative;
      }

      .badge-progress-bar-inner {
          height: 100%;
          background: #4caf50;
          border-radius: 15px;
          width: 0;
          transition: width 0.3s ease;
      }

      .badge-progress-info {
          font-size: 16px;
          color: #333;
      }

      /* Message Section */
      .achievement-status p {
          font-size: 16px;
          color: #555;
          font-weight: bold;
      }

      /* Badges List Section */
      .achievements-list {
          margin-top: 40px;
      }

      .achievements-list h2 {
          font-size: 24px;
          color: #2e3b47;
          margin-bottom: 20px;
          font-weight: 600;
      }

      /* Badge Item Cards */
      .achievement-item {
          background: #fff;
          border: 1px solid #ddd;
          padding: 20px;
          border-radius: 10px;
          margin-bottom: 20px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          display: flex;
          justify-content: space-between;
          align-items: center;
          transition: all 0.3s ease;
      }

      .achievement-item:hover {
          transform: translateY(-5px);
          box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
      }

      .achievement-item strong {
          font-size: 18px;
          color: #1a73e8;
      }

      .achievement-item span {
          font-size: 14px;
          color: #777;
      }

      /* Empty Badge Message */
      .achievements-list p {
          font-size: 16px;
          color: #777;
          text-align: center;
          font-weight: 600;
      }

      /* Responsive Styles */
      @media (max-width: 768px) {
          .container {
              padding: 20px;
          }

          h1 {
              font-size: 28px;
          }

          .achievement-item {
              padding: 15px;
          }
      }

        
    </style>
</head>
<body>
<div class="container">
    <h1>Your Achievements</h1>
    <div class="achievement-status">
        <h2>Progress Toward "Savings Champion" Badge</h2>
        <?php
        $badgeThreshold = 4;
        $remaining = max(0, $badgeThreshold - $savingsStreak);
        $progressPercent = ($badgeThreshold > 0) ? ($savingsStreak / $badgeThreshold) * 100 : 0;
        ?>
        <div class="badge-progress">
            <div class="badge-progress-bar">
                <div class="badge-progress-bar-inner" style="width: <?php echo $progressPercent; ?>%;"></div>
            </div>
            <span class="badge-progress-info"><?php echo $savingsStreak; ?>/<?php echo $badgeThreshold; ?> Days</span>
        </div>
        <p>
            <?php if ($remaining > 0): ?>
                Keep saving for <?php echo $remaining; ?> more day(s) this week to earn the "Savings Champion" badge!
            <?php else: ?>
                Congratulations! You have earned the "Savings Champion" badge!
            <?php endif; ?>
        </p>
    </div>
    <div class="achievements-list">
        <h2>Your Earned Badges</h2>
        <?php if (count($badges) > 0): ?>
            <?php foreach ($badges as $badge): ?>
                <div class="achievement-item">
                    <strong><?php echo $badge['BadgeName']; ?></strong> - Earned on <?php echo $badge['DateAchieved']; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You haven't earned any badges yet. Start saving to unlock achievements!</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
