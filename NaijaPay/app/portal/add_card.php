<?php
require_once 'header.php';
require '../db_connect.php';

$user_id = $_SESSION['user_id'];

// Handle POST to add a card
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'];
    $last_four = $_POST['last_four'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO payment_cards (user_id, card_nickname, card_last_four) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $nickname, $last_four]);
        
        $_SESSION['message'] = "Card '$nickname' added successfully! Card ID is " . $pdo->lastInsertId();
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error adding card.';
        $_SESSION['message_type'] = 'error';
    }
    header('Location: add_card.php');
    exit;
}
?>

<h2>Add Payment Card</h2>
<p>Add a new card to your profile for funding your wallet.</p>

<form action="add_card.php" method="POST" class="profile-form">
    <div class="form-group">
        <label for="nickname">Card Nickname (e.g., My Visa)</label>
        <input type="text" id="nickname" name="nickname" required>
    </div>
    <div class="form-group">
        <label for="last_four">Last Four Digits</label>
        <input type="text" id="last_four" name="last_four" pattern="\d{4}" title="Must be 4 digits" required>
    </div>
    <button type="submit" class="btn">Add Card</button>
</form>

<?php
require_once 'footer.php';
?>