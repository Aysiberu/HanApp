<?php
session_start();
require 'supabase.php';

$provider = isset($_GET['provider']) ? (int)$_GET['provider'] : 0;
$service = isset($_GET['service']) ? trim($_GET['service']) : '';

if (!$provider || !$service) {
    header('Location: bookservice.php'); exit;
}

$prov = sb_getById('providers', $provider);
?>
<!doctype html>
<html>
  <head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>HanApp — Book service</title>
  <link rel="stylesheet" href="accountinfo.css"></head>
  <body>
    <header>
      <div class="brand"><img src="image/Logo.png" alt="HanApp"></div>
      <div class="user"><button id="profileBtn" class="profile-btn"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'You'); ?><span class="caret">▾</span></button></div>
    </header>
    <div class="layout"><aside class="sidebar"><div class="nav-card"><nav><ul class="nav"><li><a href="bookservice.php">Book Service</a></li></ul></nav></div></aside>
    <main class="main"><div class="card" style="text-align:center; padding:30px;">
      <h2>Confirm booking</h2>
      <p>You selected <strong><?php echo htmlspecialchars($service); ?></strong> with <strong><?php echo htmlspecialchars($prov['name'] ?? 'selected provider'); ?></strong>.</p>
      <p><a class="btn" href="bookinghistory.php">Confirm & Go to Bookings</a> <a class="btn" href="bookservice.php">Cancel</a></p>
    </div></main></div>

  </body>
</html>
