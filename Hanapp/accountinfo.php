<?php
session_start();
include "supabase.php";

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user = sb_getById('users', $user_id);
if (!$user) {
  echo "Error: user not found.";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>HanApp — Account Information</title>
  <link rel="stylesheet" href="app.css" />
  <link rel="stylesheet" href="accountinfo.css" />
</head>

<body>
  <header>
    <div class="brand">
      <img src="image/Logo.png" alt="HanApp">
    </div>

    <div class="user">
      <button id="profileBtn" class="profile-btn" aria-haspopup="true" aria-expanded="false">
        <span style="font-size:14px; color:#333;"><?php echo htmlspecialchars($user['first_name']); ?></span>
        <span class="avatar"><img src="" alt="user"></span>
        <span class="caret">▾</span>
      </button>

      <div id="profileDropdown" class="profile-dropdown">
        <ul>
          <li><a href="bookservice.php">Book Service</a></li>
          <li><a href="accountinfo.php">Account Info</a></li>
          <li><a href="messages.php">Message</a></li>
          <li><a href="settings.html">Settings</a></li>
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
            <li><a href="accountinfo.php" class="active">Account Information</a></li>
            <li><a href="bookinghistory.html">Booking History</a></li>
            <li><a href="messages.php">Messages</a></li>
            <li><a href="settings.html">Settings</a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <main class="main">
      <div class="card">
        <div class="account-header">
          <div class="section-header">
            <div class="tabs">
              <div class="tab">Account Info</div>
            </div>
            <div class="section-title">Account Information</div>
          </div>

          <div class="fields">

            <div class="name-row">
              <div class="input-wrap">
                <label>First Name</label>
                <input type="text" value="<?php echo $user['first_name']; ?>" readonly>
              </div>

              <div class="input-wrap">
                <label>Middle Name</label>
                <input type="text" value="<?php echo $user['middle_name']; ?>" readonly>
              </div>

              <div class="input-wrap">
                <label>Last Name</label>
                <input type="text" value="<?php echo $user['last_name']; ?>" readonly>
              </div>

              <div class="input-wrap">
                <label>Extension Name</label>
                <input type="text" value="<?php echo $user['ext_name']; ?>" readonly>
              </div>
            </div>

            <div class="two-col">
              <div class="input-wrap">
                <label>Email</label>
                <input type="email" value="<?php echo $user['email']; ?>" readonly>
              </div>

              <div class="input-wrap">
                <label>Phone Number</label>
                <input type="tel" value="<?php echo $user['mobile']; ?>" readonly>
              </div>
            </div>

            <div class="two-col">
              <div class="input-wrap" style="grid-column: span 2;">
                <label>Address</label>
                <?php
                  $parts = [];
                  if (!empty($user['house_number'])) $parts[] = $user['house_number'];
                  if (!empty($user['street'])) $parts[] = $user['street'];
                  if (!empty($user['location'])) $parts[] = $user['location'];
                  if (!empty($user['zip'])) $parts[] = $user['zip'];
                  $addr = htmlspecialchars(trim(implode(', ', $parts)));
                ?>
                <input type="text" value="<?php echo $addr; ?>" readonly>
              </div>
            </div>

            <div class="bio">
              <label>Bio</label>
              <textarea readonly>This person is an example and approachable.</textarea>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Profile dropdown
    (function () {
      const btn = document.getElementById('profileBtn');
      const dd = document.getElementById('profileDropdown');

      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dd.classList.toggle('open');
      });

      document.addEventListener('click', function (e) {
        if (!dd.contains(e.target) && !btn.contains(e.target)) {
          dd.classList.remove('open');
        }
      });
    })();
  </script>
</body>

</html>