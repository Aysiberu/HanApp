<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HanApp | Log in</title>
  <link rel="stylesheet" href="app.css" />
  <link rel="stylesheet" href="loginpage.css" />
</head>
<body>
  <header>
    <img src="image/Logo.png" alt="HanApp Logo" />
    <a href="landingpage.html" style="color:#333; text-decoration:none;">Home</a>
  </header>

  <div class="login-container">
    <div class="login-box">
      <h2>Log in to HanApp</h2>

      <!-- Form uses POST and sends to login_process.php -->
      <form action="login_process.php" method="POST">
        <div class="input-group">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" placeholder="Email" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Password" required>
        </div>

        <button type="submit" class="btn">Log In</button>

        <div class="extra-links">
          <p><a href="reset_password.php">Forgot your password?</a></p>
          <p>Don't have an account? <a href="signuppage.php">Sign up</a></p>
        </div>
      </form>

      <script>
        // small client-side login checks to improve UX
        (function () {
          const form = document.querySelector('form');
          form.addEventListener('submit', function (e) {
            const emailEl = document.getElementById('email');
            const pwdEl = document.getElementById('password');
            const email = emailEl.value.trim();
            if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
              alert('Please enter a valid email address.');
              e.preventDefault();
              return;
            }
            // trim values before submit so server receives normalized input
            emailEl.value = email;
            pwdEl.value = pwdEl.value.trim();
          });
        })();
      </script>
    </div>
  </div>

  <footer>
    © 2025 HanApp. All Rights Reserved.
  </footer>
</body>
</html>

