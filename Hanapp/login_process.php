<?php
session_start();
include "supabase.php";

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// basic validation
if (empty($email) || empty($password)) {
    echo "<script>alert('Please enter both email and password.'); window.location.href='loginpage.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please provide a valid email address.'); window.location.href='loginpage.php';</script>";
    exit;
}

// Fetch user via Supabase
$rows = sb_select('users', 'select=*&email=eq.' . rawurlencode($email) . '&limit=1');
if ($rows === null) {
    echo "<script>alert('Internal error while authenticating. Please try again later.'); window.location.href='loginpage.php';</script>";
    exit;
}

if (count($rows) === 0) {
    echo "<script>alert('Email not found!'); window.location.href='loginpage.php';</script>";
    exit;
}

$user = $rows[0];

    // Verify hashed password
    if (password_verify($password, $user['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['email'] ?? '');
        header("Location: accountinfo.php");
        exit;
    } else {
        echo "<script>alert('Incorrect password!'); window.location.href='loginpage.php';</script>";
    }
?>
