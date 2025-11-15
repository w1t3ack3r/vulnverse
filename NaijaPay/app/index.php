<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - NaijaPay Staging</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container auth-page">
        <header>
            <h1>NaijaPay <span class="env-tag">STAGING</span></h1>
            <p>The future of payments in Nigeria.</p>
        </header>
        <main>
            <?php if (isset($_SESSION['user_id'])): ?>
                <h2>Welcome back!</h2>
                <p>You are already logged in.</p>
                <a href="portal/history.php" class="btn">Go to Portal</a>
                <a href="logout.php" class="btn btn-secondary">Log Out</a>
            <?php else: ?>
                <h2>Welcome to UAT</h2>
                <p>Please log in or register to test the new transaction portal.</p>
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
