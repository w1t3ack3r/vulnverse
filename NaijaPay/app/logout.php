<?php
session_start();
session_unset();
session_destroy();
setcookie('x-admin-session-id', '', time() - 3600, '/');

header('Location: index.php');
exit;
?>