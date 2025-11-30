<?php
session_start();
require 'supabase.php'; // Supabase REST helper

// If user did not come from send_code.php, block access
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['verification_code'];

    // Get the stored code and expiry from the database via Supabase
    $rows = sb_select('users', 'select=reset_code,reset_expiry&email=eq.' . rawurlencode($email));
    $row = (!empty($rows) && isset($rows[0])) ? $rows[0] : null;

    $stored_code = $row['reset_code'] ?? null;
    $stored_expiry = $row['reset_expiry'] ?? null;

    // Compare and ensure code is not expired
    $now = time();
    $expired = $stored_expiry && strtotime($stored_expiry) < $now;

    if ($input_code == $stored_code && !$expired) {

        // Clear reset code once used (optional)
        sb_update('users', 'email=eq.' . rawurlencode($email), ['reset_code' => null, 'reset_expiry' => null]);

        // Go to reset password page
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Incorrect verification code. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Code</title>
    <link rel="stylesheet" href="forgot.css">
    <style>
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        .back-btn {
            margin-top: 15px;
            display: inline-block;
            color: #008f79;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Verify Code</h2>
    <p>A verification code has been sent to your email.</p>

    <form method="POST">
        <input type="number" name="verification_code" required placeholder="Enter 6-digit code">
        <button type="submit">Verify</button>
    </form>

    <?php if ($error) { echo "<div class='error'>$error</div>"; } ?>

    <a href="forgot_password.php" class="back-btn">← Back</a>
</div>

</body>
</html>
