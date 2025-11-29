<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'supabase.php';

$sender = isset($_POST['sender']) ? (int)$_POST['sender'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
$receiver = isset($_POST['receiver']) ? (int)$_POST['receiver'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$sender || !$receiver || $message === '') {
    echo json_encode(['success'=>false,'error'=>'Missing parameters']);
    exit;
}

$resp = sb_insert('messages', ['sender_id' => $sender, 'receiver_id' => $receiver, 'message' => $message]);
if ($resp['success']) {
    $inserted = is_array($resp['data']) && count($resp['data']) ? $resp['data'][0] : null;
    echo json_encode(['success' => true, 'data' => $inserted]);
} else {
    echo json_encode(['success' => false, 'error' => $resp['error'] ?? 'unknown']);
}

?>
