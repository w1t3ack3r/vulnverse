<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get the vulnerable firstname.
// Notice Bayo didn't use htmlspecialchars() here!
$current_user_name = $_SESSION['user_firstname'] ?? $_SESSION['user_email'];

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Portal - NaijaPay</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <nav class="portal-nav">
            <h1>NaijaPay <span class="env-tag">Portal</span></h1>
            <div class="user-info">
                Logged in as <strong><?php echo $current_user_name; ?></strong>
                (<a href="profile.php">Profile</a> | <a href="transfer.php">Transfer</a> | <a href="fund.php">Fund</a> | <a href="add_card.php">Add Card</a> | <a href="../logout.php">Logout</a>)
            </div>
        </nav>
        <main>
            <?php if ($message): ?>
                <div class="message <?php echo (isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'error') ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php 
                unset($_SESSION['message_type']);
            endif; 
            ?>