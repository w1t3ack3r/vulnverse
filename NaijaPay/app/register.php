<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db_connect.php';
    
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $bvn = $_POST['bvn'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($firstname) || empty($lastname) || empty($bvn) || empty($email) || empty($password)) {
        $message = 'All fields are required.';
    } elseif (strlen($bvn) !== 11 || !ctype_digit($bvn)) {
        $message = 'Invalid BVN. Must be 11 digits.';
    } else {
        $pdo->beginTransaction();
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // 1. Create the user
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, bvn, email, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $bvn, $email, $password_hash]);
            $new_user_id = $pdo->lastInsertId();
            
            // 2. Create their default "Savings" account with 10k NGN
            $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_name, balance) VALUES (?, 'My Savings', 10000.00)");
            $stmt->execute([$new_user_id]);

            $pdo->commit();
            $message = 'Registration successful! You can now <a href="login.php">log in</a>.';

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                $message = 'This email address is already registered.';
            } else {
                $message = 'An error occurred: ' . $e->getMessage(); // Better error reporting for us
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NaijaPay Staging</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container auth-page">
        <header>
            <h1>NaijaPay <span class="env-tag">STAGING</span></h1>
            <h2>Create UAT Account</h2>
        </header>
        <main>
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="bvn">BVN (11 digits)</label>
                    <input type="text" id="bvn" name="bvn" pattern="\d{11}" title="Must be 11 digits" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="login.php">Login here</a>.</p>
        </main>
    </div>
</body>
</html>