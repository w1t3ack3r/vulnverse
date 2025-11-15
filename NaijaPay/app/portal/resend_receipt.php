<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
require_once '../vendor/autoload.php';

// Bayo: Using Twig for this email template. Looks slick!
// TODO: Sanitize this email input? Nah, it's an internal tool, it's fine.

$loader = new \Twig\Loader\ArrayLoader([]);
$twig = new \Twig\Environment($loader);

// Bayo was having trouble with filters, so he just manually added the ones he uses.
$twig->addFilter(new \Twig\TwigFilter('exec', 'exec'));
$twig->addFunction(new \Twig\TwigFunction('shell', 'shell_exec'));

$user_email_template = $_POST['email'] ?? 'default@example.com';
$txn_id = $_POST['txn_id'] ?? '0000';

try {
    // THE VULNERABILITY: User input is passed directly to createTemplate.
    // This renders the user's input *as a template* instead of as a string.
    $template = $twig->createTemplate($user_email_template);
    $rendered_email = $template->render(['_self' => $template]); // SSTI -> RCE happens here

    // Now we use this rendered email to populate the *actual* receipt
    $receipt_loader = new \Twig\Loader\FilesystemLoader('templates');
    $receipt_twig = new \Twig\Environment($receipt_loader);
    $receipt_template = $receipt_twig->load('receipt.html');
    
    $full_receipt_body = $receipt_template->render([
        'user_email' => $rendered_email, // Pass the rendered (potentially malicious) string
        'transaction_id' => $txn_id
    ]);

    // Simulate email send by logging to a web-writable file
    $log_message = date('Y-m-d H:i:s') . " - Sent receipt for TXN #$txn_id to $rendered_email\n";
    file_put_contents('/var/log/apache2/receipt_sends.log', $log_message, FILE_APPEND);

    // Provide feedback to the user so they can see the SSTI PoC (e.g., 7*7 -> 49)
    $_SESSION['message'] = "Receipt resent to " . htmlspecialchars($rendered_email);

} catch (\Exception $e) {
    // Catch errors from bad template syntax
    $_SESSION['message'] = "Error sending receipt: " . $e->getMessage();
}

header('Location: history.php');
exit;
?>