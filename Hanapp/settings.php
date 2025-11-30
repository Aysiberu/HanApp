<?php
session_start();
include "supabase.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// fetch user row
$sql = "SELECT * FROM users WHERE id = ?";
// load user via Supabase
$user = sb_getById('users', $user_id);


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>HanApp — Settings (Security)</title>
  <link rel="stylesheet" href="app.css"> <!-- shared layout and colors -->
  <link rel="stylesheet" href="accountinfo.css"> <!-- your main CSS -->
  <link rel="stylesheet" href="settings.css"> <!-- settings-specific additions -->
  <meta name="viewport" content="width=device-width,initial-scale=1" />
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
          <li><a href="bookinghistory.php">Booking History</a></li>
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
            <li><a href="bookinghistory.php">Booking History</a></li>
            <li><a href="messages.php">Messages</a></li>
            <li><a class="active" href="settings.php">Settings</a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <main class="main">
      <div class="card">
        <div class="section-header">
          <div class="tabs">
            <a href="settings.php" class="tab">Account Setting</a>
            <a href="settings_security.php" class="tab active">Security</a>
            <!-- Notifications removed (real-time notifications not in use) -->
          </div>
          <div class="section-title">Security</div>
        </div>

        <div class="fields">
          <!-- Devices list -->
          <div style="grid-column: span 2;">
            <h3>Devices that you are logged in</h3>
            <table style="width:100%; margin-top:10px; border-collapse:collapse;">
              <thead>
                <tr style="text-align:left; color:var(--muted);">
                  <th>Model</th>
                  <th>Device Type</th>
                  <th>Time and Date Logged in</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($devices as $d): ?>
                <tr>
                  <td><?php echo htmlspecialchars($d['model']); ?></td>
                  <td><?php echo htmlspecialchars($d['type']); ?></td>
                  <td><?php echo htmlspecialchars($d['logged']); ?></td>
                  <td><button class="small red" data-remove="1">Remove ✖</button></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Two-Factor Authentication feature removed -->

          <!-- Suspicious activity alerts removed -->

          <!-- Verification method -->
          <div style="grid-column: span 2; margin-top:18px;">
            <h3>Verification Method</h3>
            <p style="color:var(--muted);">Choose how you want to receive verification codes when required.</p>

            <div class="verify-grid">
              <div class="verify-box">
                <div style="font-weight:700">Email</div>
                <div style="color:var(--muted)"><?php echo htmlspecialchars($user['email']); ?></div>
                <div style="margin-top:8px;">
                  <label class="switch">
                    <input id="verifyEmailToggle" type="checkbox" <?php echo $user['verify_email'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                  </label>
                  <span style="margin-left:10px;">Use Email</span>
                </div>
              </div>

              <div class="verify-box">
                <div style="font-weight:700">Mobile Number (disabled)</div>
                <div style="color:var(--muted)"><?php echo htmlspecialchars($user['mobile']); ?></div>
                <div style="margin-top:8px;">
                  <small style="color:var(--muted);">SMS option is not available yet.</small>
                </div>
              </div>
            </div>

            <div id="verifyError" style="color:#b30000; margin-top:10px; display:none;"></div>
          </div>

          <!-- Change password -->
          <div style="grid-column: span 2; margin-top:20px;">
            <h3>Change Password</h3>
            <p style="color:var(--muted);">Click the button to change your password.</p>
            <a class="btn" href="forgot_password.php">Change Password</a>
          </div>

        </div> <!-- fields -->
      </div> <!-- card -->
    </main>
  </div>

<script>
  // profile dropdown
  (function(){
    const btn = document.getElementById('profileBtn');
    const dd = document.getElementById('profileDropdown');
    btn.addEventListener('click', function(e){ e.stopPropagation(); dd.classList.toggle('open'); });
    document.addEventListener('click', function(e){ if(!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.remove('open'); });
  })();

  // DOM bindings (2FA & suspicious alerts removed)
  const verifyEmailToggle = document.getElementById('verifyEmailToggle');
  const verifyError = document.getElementById('verifyError');

  function postJSON(url, data){
    return fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify(data),
      credentials: 'same-origin'
    }).then(res => res.json());
  }

  // Two-factor and suspicious alert handlers removed

  // Toggle verification method (email)
  verifyEmailToggle.addEventListener('change', async function(){
    const enable = this.checked ? 1 : 0;
    const res = await postJSON('update_settings.php', {action:'verify_email', enable: enable});
    if (res.success){
      verifyError.style.display='none';
    } else {
      // server rejects disabling the last verification method
      verifyError.style.display='block';
      verifyError.textContent = res.message || 'You must have at least one verification method enabled.';
      // revert toggle
      verifyEmailToggle.checked = !enable;
    }
  });

</script>
</body>
</html>
