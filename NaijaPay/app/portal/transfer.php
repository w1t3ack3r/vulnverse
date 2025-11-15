<?php
require_once 'header.php'; 
require '../db_connect.php';

// Get the user's own accounts to populate the 'From' dropdown
$stmt = $pdo->prepare("SELECT id, account_name, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_accounts = $stmt->fetchAll();
?>

<h2>New Transfer</h2>
<p>Move funds to any other NaijaPay user.</p>

<form action="do_transfer.php" method="POST" class="profile-form">
    
    <div class="form-group">
        <label for="from_account">From Account:</label>
        <select name="from_account_id" id="from_account" class="form-control">
            <?php foreach ($user_accounts as $account): ?>
                <option value="<?php echo $account['id']; ?>">
                    <?php echo htmlspecialchars($account['account_name']); ?> 
                    (ID: <?php echo $account['id']; ?>) - 
                    NGN <?php echo number_format($account['balance'], 2); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="recipient_email">Recipient's Email:</label>
        <input type="email" id="recipient_email" name="recipient_email" required>
    </div>

    <div class="form-group">
        <label for="amount">Amount (NGN):</label>
        <input type="number" id="amount" name="amount" step="0.01" min="1" required>
    </div>
    
    <div class="form-group">
        <label for="memo">Memo (Optional):</label>
        <input type="text" id="memo" name="memo">
    </div>

    <button type="submit" class="btn">Send Money</button>
</form>

<?php
require_once 'footer.php'; 
?>