<?php
session_start();
require 'supabase.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch booking history via Supabase
$query = sprintf('select=*&user_id=eq.%d&order=date_booked.desc', $user_id);
$result = sb_select('bookings', $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>HanApp — Booking History</title>
  <link rel="stylesheet" href="app.css" />
  <link rel="stylesheet" href="bookinghistory.css" />
</head>

<body>

  <header>
    <div class="brand">
      <img src="image/Logo.png" alt="HanApp">
    </div>

    <div class="user">
      <button id="profileBtn" class="profile-btn">
        <span style="font-size:14px; color:#333;">
          <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
        <span class="avatar"><img src="" alt="user"></span>
        <span class="caret">▾</span>
      </button>

      <div id="profileDropdown" class="profile-dropdown">
        <ul>
          <li><a href="landingpage.php">Book Service</a></li>
          <li><a href="accountinfo.php">Account Info</a></li>
          <li><a href="messages.php">Message</a></li>
          <li><a href="settings.php">Settings</a></li>
          <li><a href="logout.php" id="logoutLink">Log Out</a></li>
        </ul>
      </div>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <div class="nav-card">
        <nav>
          <ul class="nav">
            <li><a href="accountinfo.php">Account Information</a></li>
            <li><a class="active" href="bookinghistory.php">Booking History</a></li>
            <li><a href="messages.php">Message</a></li>
            <li><a href="settings.php">Settings</a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <main class="main">
      <div class="card">
        <div class="account-header">
          <div class="account-info">

            <div class="section-header">
              <div class="tabs">
                <div class="tab">Booking History</div>
              </div>
              <div class="section-title">Booking History</div>
            </div>

            <div class="fields">
              <div class="booking-list" style="grid-column: span 2;">

                <?php if (!empty($result)): ?>
                  <?php foreach ($result as $row): ?>

                    <div class="booking">
                      <div class="left">
                        <div class="photo">
                          <img src="<?php echo $row['provider_photo'] ?: 'https://i.pravatar.cc/120'; ?>" alt="provider">
                        </div>
                        <?php
                          // resolve provider id by matching user record if possible
                          $providerId = 0;
                          $providerName = trim($row['provider_name']);
                          if ($providerName) {
                            $parts = explode(' ', $providerName, 2);
                            if (count($parts) === 2) {
                              $filter = sprintf('select=id&first_name=eq.%s&last_name=eq.%s', rawurlencode($parts[0]), rawurlencode($parts[1]));
                              $pr = sb_select('users', $filter);
                              if (!empty($pr) && isset($pr[0]['id'])) $providerId = (int)$pr[0]['id'];
                            }
                          }
                        ?>
                        <a class="msg-btn" href="<?php echo $providerId ? 'messages.php?to='.$providerId : 'messages.php'; ?>">Message</a>
                      </div>

                      <div class="right">
                        <h3>
                          <?php echo htmlspecialchars($row['provider_name']); ?>
                          <small style="color:var(--muted); font-weight:600">
                            <?php echo htmlspecialchars($row['service_type']); ?>
                          </small>
                        </h3>

                        <table class="booking-table">
                          <tr>
                            <td>
                              <span class="label">Date You Booked:</span>
                              <span class="value"><?php echo $row['date_booked']; ?></span>
                            </td>
                            <td>
                              <span class="label">Location:</span>
                              <span class="value"><?php echo $row['location']; ?></span>
                            </td>
                            <td>
                              <span class="label">Booking Status:</span>
                              <span class="value"><?php echo $row['status']; ?></span>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <span class="label">Contact:</span>
                              <span class="value"><?php echo $row['contact']; ?></span>
                            </td>
                            <td>
                              <span class="label">Schedule:</span>
                              <span class="value">
                                <?php echo date("M d, Y (gA - gA)", strtotime($row['schedule'])); ?>
                              </span>
                            </td>
                            <td>
                              <span class="label">Receipt Number:</span>
                              <span class="value"><?php echo $row['receipt_number']; ?></span>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>

                  <?php endforeach; ?>
                <?php else: ?>
                  <p>No bookings yet.</p>
                <?php endif; ?>

              </div>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // dropdown script (same as your HTML version)
  </script>

</body>

</html>