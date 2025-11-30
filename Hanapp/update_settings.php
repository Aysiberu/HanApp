<?php
session_start();
header('Content-Type: application/json');
include "supabase.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'Not logged in']);
    exit;
}

$uid = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;


// load current user using Supabase
$user = sb_getById('users', $uid);

// The two-factor authentication and suspicious notification flows have been removed.
// Only verification method (email) toggling remains below.

$action = $input['action'] ?? '';

if ($action === 'verify_email') {
    $enable = intval($input['enable']);
    // if turning off verify_email, ensure we don't leave both methods disabled.
    // Since only email exists, disallow disabling it entirely.
    if ($enable === 0) {
        echo json_encode(['success'=>false, 'message'=>'Email verification cannot be disabled because it is the only verification method.' ]);
        exit;
    } else {
        $res = sb_update('users', 'id=eq.' . intval($uid), ['verify_email' => $enable]);
        if ($res['success']) echo json_encode(['success'=>true]);
        else echo json_encode(['success'=>false,'message'=>'Failed to update verification method.']);
    }
    exit;
}

echo json_encode(['success'=>false,'message'=>'Invalid action']);
exit;
