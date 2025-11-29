<?php
session_start();
include "supabase.php";

if (!isset($_SESSION["reset_email"])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION["reset_email"];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="forgot.css">
    <title>Reset Password</title>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <form action="reset_password_process.php" method="POST">
        <input type="password" name="password" required placeholder="New password">
        <button type="submit">Reset Password</button>
    </form>
</div>

</body>
</html>
