<?php
session_start();
include "supabase.php";

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    echo "<script>alert('Please enter both email and password.'); window.location.href='provider_login.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please provide a valid email address.'); window.location.href='provider_login.php';</script>";
    exit;
}

$rows = sb_select('users', 'select=*&email=eq.' . rawurlencode($email) . '&limit=1');
if ($rows === null) {
    echo "<script>alert('Internal error while authenticating. Please try again later.'); window.location.href='provider_login.php';</script>";
    exit;
}

if (count($rows) === 0) {
    echo "<script>alert('Email not found!'); window.location.href='provider_login.php';</script>";
    exit;
}

$user = $rows[0];

if (!password_verify($password, $user['password'])) {
    echo "<script>alert('Incorrect password!'); window.location.href='provider_login.php';</script>";
    exit;
}

// verify provider record exists
$prov = sb_select('providers', 'select=*&user_id=eq.' . intval($user['id']) . '&limit=1');
if ($prov === null) {
    echo "<script>alert('Internal error.'); window.location.href='provider_login.php';</script>";
    exit;
}
if (count($prov) === 0) {
    echo "<script>alert('No provider account found for this user.'); window.location.href='provider_signup.php';</script>";
    exit;
}

// success
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['email'] ?? '');
header('Location: provider_messages.php');
exit;

?>
