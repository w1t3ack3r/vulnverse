<?php
session_start();
require '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fund.php');
    exit;
}

// --- VULNERABLE PARAMETERS ---
$card_id = $_POST['card_id']; // IDOR Flaw
$to_account_id = $_POST['to_account_id'];
$amount = (float)$_POST['amount'];
$sender_user_id = $_SESSION['user_id'];
// ---

$pdo->beginTransaction();

try {
    // 1. --- IDOR FLAW ---
    // Bayo just trusts the $card_id from the form. 
    // He NEVER checks if the logged-in user actually OWNS this card!
    $stmt = $pdo->prepare("SELECT * FROM payment_cards WHERE id = ? AND is_active = TRUE");
    $stmt->execute([$card_id]);
    $payment_card = $stmt->fetch();

    if (!$payment_card) {
        throw new Exception("Payment card not found or inactive.");
    }
    
    // 2. Security Check (Flawed): Check that the destination account belongs to the sender
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ? AND user_id = ? FOR UPDATE");
    $stmt->execute([$to_account_id, $sender_user_id]);
    $recipient_account = $stmt->fetch();

    if (!$recipient_account) {
        throw new Exception("Destination account is invalid or does not belong to you.");
    }

    // 3. Process the funding (Add to the user's account)
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $recipient_account['id']]);
    
    // 4. Log the funding transaction (from the card to the account)
    $stmt = $pdo->prepare("INSERT INTO transactions (from_account_id, to_account_id, amount, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$payment_card['id'], $recipient_account['id'], $amount, "Fund Wallet from Card **{$payment_card['card_last_four']}**"]);

    $pdo->commit();
    $_SESSION['message'] = 'Wallet successfully funded!';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message'] = 'Funding failed: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: history.php');
exit;
?>