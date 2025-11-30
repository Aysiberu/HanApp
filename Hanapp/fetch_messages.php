<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'supabase.php';

$u = isset($_GET['u']) ? (int)$_GET['u'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
$other = isset($_GET['other']) ? (int)$_GET['other'] : 0;

if (!$u || !$other) {
    echo json_encode([]);
    exit;
}

// PostgREST OR filter (matching both directions)
$filters = sprintf('select=id,sender_id,receiver_id,message,created_at&or=(and(sender_id.eq.%d,receiver_id.eq.%d),and(sender_id.eq.%d,receiver_id.eq.%d))&order=created_at.asc', $u, $other, $other, $u);
$rows = sb_select('messages', $filters);
echo json_encode($rows ?: []);

?>
