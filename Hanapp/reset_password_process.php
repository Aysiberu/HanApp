<?php
session_start();
include "supabase.php";

$email = $_SESSION["reset_email"];
$newpass = password_hash($_POST["password"], PASSWORD_DEFAULT);

sb_update('users', 'email=eq.' . rawurlencode($email), ['password' => $newpass, 'reset_code' => null, 'reset_expiry' => null]);

session_unset();
session_destroy();

echo "<script>alert('Password successfully reset!'); window.location='loginpage.php';</script>";
