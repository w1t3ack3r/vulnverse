<?php
require_once 'header.php'; 
require '../db_connect.php';

// Get all accounts and balances for the logged-in user
$stmt = $pdo->prepare("SELECT id, account_name, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$accounts = $stmt->fetchAll();

$total_balance = 0;
$account_ids = [];
foreach ($accounts as $account) {
    $total_balance += $account['balance'];
    $account_ids[] = $account['id'];
}

// --- Dynamic Transaction Log Fetch ---
// Find transactions where the user is either the sender OR the receiver.
// This handles transfers AND funding.
$placeholders = implode(',', array_fill(0, count($account_ids), '?'));
$stmt = $pdo->prepare("SELECT id, from_account_id, to_account_id, amount, description, txn_date FROM transactions WHERE from_account_id IN ($placeholders) OR to_account_id IN ($placeholders) ORDER BY txn_date DESC");

// Execute with the account IDs twice (for both WHERE clauses)
$params = array_merge($account_ids, $account_ids);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Utility function to determine if the transaction is a DEBIT or CREDIT for the user
function get_txn_type($txn, $user_account_ids) {
    if (in_array($txn['from_account_id'], $user_account_ids)) {
        return 'DEBIT'; // Money moved *from* a user account (transfer out)
    }
    return 'CREDIT'; // Money moved *to* a user account (transfer in or funding)
}
?>

<h2>Your Accounts</h2>

<?php if ($total_balance > 50000): ?>
<div class="message success flag-box">
    <strong>Congratulations!</strong> You've successfully performed the transfer.<br>
    Here is your flag:<br>
    <strong>flag{n3g4t1v3_pr0f1t_and_id0r_pwn}</strong>
</div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Account ID</th>
            <th>Account Name</th>
            <th>Balance (NGN)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($accounts as $account): ?>
        <tr>
            <td><?php echo $account['id']; ?></td>
            <td><?php echo htmlspecialchars($account['account_name']); ?></td>
            <td><?php echo number_format($account['balance'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-balance">
            <td colspan="2">Total Balance:</td>
            <td><?php echo number_format($total_balance, 2); ?></td>
        </tr>
    </tbody>
</table>

<br>
<h2>Live Transaction History</h2>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Amount (NGN)</th>
            <th>From/To Account ID</th>
            <th>Action (SSTI Vulnerability)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($transactions)): ?>
            <tr><td colspan="6">No recent transactions.</td></tr>
        <?php else: ?>
            <?php foreach ($transactions as $txn): 
                $txn_type = get_txn_type($txn, $account_ids);
                $display_amount = ($txn_type == 'DEBIT') ? -$txn['amount'] : $txn['amount'];
                $status_class = ($txn_type == 'DEBIT') ? 'status-failed' : 'status-completed';
                $other_account_id = ($txn_type == 'DEBIT') ? $txn['to_account_id'] : $txn['from_account_id'];
            ?>
                <tr>
                    <td><?php echo date('M d, Y H:i:s', strtotime($txn['txn_date'])); ?></td>
                    <td><span class="<?php echo $status_class; ?>"><?php echo $txn_type; ?></span></td>
                    <td><?php echo htmlspecialchars($txn['description']); ?></td>
                    <td><?php echo number_format($display_amount, 2); ?></td>
                    <td>ID: <?php echo $other_account_id; ?></td>
                    <td>
                        <form action="resend_receipt.php" method="POST" style="margin:0;">
                            <input type="hidden" name="txn_id" value="<?php echo $txn['id']; ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
                            <button type="submit" class="action-btn">View Receipt</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
require_once 'footer.php'; 
?>