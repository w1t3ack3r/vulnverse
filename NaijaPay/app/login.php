<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db_connect.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, email, password_hash, firstname FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Password is correct! Start the session.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_firstname'] = $user['firstname'];

            $cookie_name = 'x-admin-session-id';
            $cookie_value = 'flag{b4y0_d03snt_kn0w_ab0ut_httponly}';
            setcookie($cookie_name, $cookie_value, 0, '/');
            
            header('Location: portal/history.php');
            exit;
        } else {
            $message = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $message = 'Database error. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NaijaPay Staging</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container auth-page">
        <header>
            <h1>NaijaPay <span class="env-tag">STAGING</span></h1>
            <h2>Portal Login</h2>
        </header>
        <main>
            <?php if ($message): ?>
                <div class="message error"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Log In</button>
            </form>
            <p class="auth-switch">Need an account? <a href="register.php">Register here</a>.</p>
        </main>
    </div>
</body>
</html>
