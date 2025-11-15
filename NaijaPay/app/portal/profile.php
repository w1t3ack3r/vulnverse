<?php
session_start();
require '../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $safe_firstname = str_replace(["<script>", "onclick"], "", $firstname);
    $safe_lastname = str_replace(["<script>", "onclick"], "", $lastname);

    try {
        $stmt = $pdo->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE id = ?");
        $stmt->execute([$safe_firstname, $safe_lastname, $user_id]);
        
        // Update the session variable
        $_SESSION['user_firstname'] = $safe_firstname;
        
        $_SESSION['message'] = 'Profile updated successfully!';
        
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error updating profile: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: profile.php');
    exit;
}


require_once 'header.php'; 

// Get current user data to populate the form
$stmt = $pdo->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<h2>Your Profile</h2>
<p>Update your personal information.</p>

<form action="profile.php" method="POST" class="profile-form">
    <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">
    </div>
    <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>">
    </div>
    <div class="form-group">
        <label for="email">Email (Read-only)</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
    </div>
    <button type="submit" class="btn">Update Profile</button>
</form>

<?php
require_once 'footer.php';
?>
