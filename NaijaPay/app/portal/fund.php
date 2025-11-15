<?php
require_once 'header.php';
require '../db_connect.php';

// Get the user's accounts to populate the 'To Account' dropdown
$stmt = $pdo->prepare("SELECT id, account_name, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_accounts = $stmt->fetchAll();

// Get the user's cards to populate the 'From Card' dropdown
$stmt = $pdo->prepare("SELECT id, card_nickname, card_last_four FROM payment_cards WHERE user_id = ? AND is_active = TRUE");
$stmt->execute([$_SESSION['user_id']]);
$user_cards = $stmt->fetchAll();

?>

<h2>Fund Wallet</h2>
<p>Fund your NaijaPay account using a saved payment card.</p>

<form action="do_fund.php" method="POST" class="profile-form">
    
    <div class="form-group">
        <label for="from_card">Pay With Card:</label>
        <select name="card_id" id="from_card" class="form-control">
            <?php foreach ($user_cards as $card): ?>
                <option value="<?php echo $card['id']; ?>">
                    <?php echo htmlspecialchars($card['card_nickname']); ?> 
                    (ID: <?php echo $card['id']; ?>) - 
                    Ending <?php echo $card['card_last_four']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="to_account">Fund Which Account:</label>
        <select name="to_account_id" id="to_account" class="form-control">
            <?php foreach ($user_accounts as $account): ?>
                <option value="<?php echo $account['id']; ?>">
                    <?php echo htmlspecialchars($account['account_name']); ?> 
                    (ID: <?php echo $account['id']; ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="amount">Amount (NGN):</label>
        <input type="number" id="amount" name="amount" step="0.01" min="1" required>
    </div>

    <button type="submit" class="btn">Fund Account</button>
</form>

<?php
require_once 'footer.php';
?>