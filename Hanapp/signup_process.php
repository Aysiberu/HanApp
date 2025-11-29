<?php
include "supabase.php";

// Get & sanitize form values
$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$middleName = isset($_POST['middleName']) ? trim($_POST['middleName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$extName = isset($_POST['extName']) ? trim($_POST['extName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

// Basic validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='signuppage.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please provide a valid email address.'); window.location.href='signuppage.php';</script>";
    exit;
}

if (strlen($password) < 8) {
    echo "<script>alert('Password must be at least 8 characters.'); window.location.href='signuppage.php';</script>";
    exit;
}

// Check password match
if ($password !== $confirmPassword) {
    echo "<script>alert('Passwords do not match!'); window.location.href='signuppage.php';</script>";
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$payload = [
    'first_name' => $firstName,
    'middle_name' => $middleName,
    'last_name' => $lastName,
    'ext_name' => $extName,
    'email' => $email,
    'mobile' => $mobile,
    'location' => $location,
    'zip' => $zip,
    'password' => $hashedPassword,
];

// Ensure email isn't already registered
$existing = sb_select('users', 'select=id&email=eq.' . rawurlencode($email));
if ($existing === null) {
    // Supabase error
    echo "<script>alert('An internal error occurred. Please try again later.'); window.location.href='signuppage.php';</script>";
    exit;
}
if (count($existing) > 0) {
    echo "<script>alert('Email already in use. Please log in or use another email.'); window.location.href='signuppage.php';</script>";
    exit;
}

$resp = sb_insert('users', $payload);
if (isset($resp['success']) && $resp['success']) {
    echo "<script>alert('Account successfully created! You may now log in.'); window.location.href='loginpage.php';</script>";
} else {
    $msg = 'An error occurred while creating the account.';
    if (!empty($resp['error'])) $msg .= ' ' . htmlspecialchars($resp['error']);
    echo "<script>alert('" . addslashes($msg) . "'); window.location.href='signuppage.php';</script>";
}
?>
