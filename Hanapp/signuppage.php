<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HanApp | Sign up</title>
  <link rel="stylesheet" href="app.css" />
  <link rel="stylesheet" href="signuppage.css" />
</head>
<body>
  <header>
  <img src="image/Logo.png" alt="HanApp Logo" />
    <a href="landingpage.html">Home</a>
  </header>

  <div class="container">
    <div class="card">
      <h2 class="heading">Personal Information</h2>
      <div class="divider"></div>

      <!-- Updated form: POST to signup_process.php -->
      <form action="signup_process.php" method="POST">

        <div class="row-4">
          <div>
            <label for="firstName">First Name</label>
            <input id="firstName" name="firstName" type="text" required>
          </div>

          <div class="mi">
            <label for="middleName">Middle Name</label>
            <input id="middleName" name="middleName" type="text">
          </div>

          <div>
            <label for="lastName">Last Name</label>
            <input id="lastName" name="lastName" type="text" required>
          </div>

          <div class="ext">
            <label for="extName">Extension Name</label>
            <input id="extName" name="extName" type="text" maxlength="6">
          </div>
        </div>

        <div class="row-2">
          <div>
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" required>
          </div>

          <div>
            <label for="mobile">Mobile Number</label>
            <input id="mobile" name="mobile" type="tel" required>
          </div>
        </div>

        <div class="row-2">
          <div>
            <label for="location">Location</label>
            <select id="location" name="location" required>
              <option value="">-- Select Municipality / City --</option>
              <option>Adams</option>
              <option>Bacarra</option>
              <option>Badoc</option>
              <option>Bangui</option>
              <option>Banna</option>
              <option>Burgos</option>
              <option>Carasi</option>
              <option>Currimao</option>
              <option>Dingras</option>
              <option>Dumalneg</option>
              <option>Laoag</option>
              <option>Marcos</option>
              <option>Nueva Era</option>
              <option>Pagudpud</option>
              <option>Piddig</option>
              <option>Pinili</option>
              <option>San Nicolas</option>
              <option>Sarrat</option>
              <option>Solsona</option>
              <option>Vintar</option>
            </select>
          </div>

          <div>
            <label for="zip">ZIP Code</label>
            <input id="zip" name="zip" type="text" inputmode="numeric" pattern="\d*" required>
          </div>
        </div>

        <div class="row-2">
          <div>
            <label for="houseNumber">House / Unit No.</label>
            <input id="houseNumber" name="houseNumber" type="text" placeholder="e.g. 12A" />
          </div>
          <div>
            <label for="street">Street / Barangay</label>
            <input id="street" name="street" type="text" placeholder="Street name, barangay" />
          </div>
        </div>

        <div class="row-2">
          <div>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" minlength="8" required>
          </div>

          <div>
            <label for="confirmPassword">Confirm Password</label>
            <input id="confirmPassword" name="confirmPassword" type="password" minlength="8" required>
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn">Sign Up</button>
        </div>

        <div class="small-note">
          Already have an account? 
          <a href="loginpage.php" style="color:#00a68e; text-decoration:none;">Log in</a>
        </div>
      </form>
    </div>
  </div>

    <footer>© 2025 HanApp. All Rights Reserved.</footer>

  <script>
    // small client-side validation
        (function () {
      const form = document.querySelector('form');
      form.addEventListener('submit', function (e) {
        const email = document.getElementById('email').value.trim();
        const pwd = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
          alert('Please provide a valid email address.');
          e.preventDefault();
          return;
        }
        if (pwd.length < 8) {
          alert('Password must be at least 8 characters.');
          e.preventDefault();
          return;
        }
        if (pwd !== confirm) {
          alert('Passwords do not match.');
          e.preventDefault();
          return;
        }
        });

      // set numeric-only enforcement for mobile and zip
      const mobileEl = document.getElementById('mobile');
      const zipEl = document.getElementById('zip');
      function onlyDigits(){ this.value = this.value.replace(/\D/g,''); }
      if (mobileEl) mobileEl.addEventListener('input', onlyDigits);
      if (zipEl) zipEl.addEventListener('input', onlyDigits);
    })();
  </script>
</body>
</html>
