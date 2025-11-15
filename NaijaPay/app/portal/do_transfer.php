<?php
session_start();
require '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transfer.php');
    exit;
}

$from_account_id = $_POST['from_account_id'];
$recipient_email = $_POST['recipient_email'];
$amount = (float)$_POST['amount'];
$memo = $_POST['memo'] ?? 'Transfer';
$sender_user_id = $_SESSION['user_id'];

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ? FOR UPDATE");
    $stmt->execute([$from_account_id]);
    $sender_account = $stmt->fetch();

    // Get the recipient
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$recipient_email]);
    $recipient_user = $stmt->fetch();
    
    if (!$recipient_user) {
        throw new Exception("Recipient not found.");
    }

    // Get recipient's primary account
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ? LIMIT 1 FOR UPDATE");
    $stmt->execute([$recipient_user['id']]);
    $recipient_account = $stmt->fetch();

    if (!$sender_account || !$recipient_account) {
        throw new Exception("Account not found.");
    }

    if ($sender_account['balance'] >= $amount) {
        
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $sender_account['id']]);

        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $recipient_account['id']]);
        
        // 3. Log the transaction (in the old table, for fun)
        $stmt = $pdo->prepare("INSERT INTO transactions (from_account_id, to_account_id, amount, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sender_account['id'], $recipient_account['id'], $amount, $memo]);

        $pdo->commit();
        $_SESSION['message'] = 'Transfer successful!';
        
    } else {
        throw new Exception("Insufficient funds.");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message'] = 'Transfer failed: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: history.php');
exit;
?>
