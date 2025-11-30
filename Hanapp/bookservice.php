<?php
session_start();
include 'supabase.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit;
}

$user_id = $_SESSION['user_id'];
// load user from Supabase
$user = sb_getById('users', $user_id);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>HanApp — Book Service</title>
    <link rel="stylesheet" href="accountinfo.css">
    <link rel="stylesheet" href="bookservice.css">
</head>

<body>
    <header>
        <div class="brand"><img src="image/Logo.png" alt="HanApp"></div>
        <div class="user">
            <button id="profileBtn" class="profile-btn">
                <span
                    style="font-size:14px; color:#333"><?php echo htmlspecialchars($user['first_name'] ?? 'You'); ?></span>
                <span class="avatar"><img src="" alt="user"></span>
                <span class="caret">▾</span>
            </button>
            <div id="profileDropdown" class="profile-dropdown">
                <ul>
                    <li><a href="bookservice.php">Book Service</a></li>
                    <li><a href="accountinfo.php">Account Info</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Log Out</a></li>
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
                        <li><a href="settings.php">Settings</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="main">
            <div class="card">
                <div class="section-header">
                    <div class="tabs">
                        <div class="tab">Book Service</div>
                    </div>
                    <div class="section-title">Book your service</div>
                </div>

                <div class="fields">
                    <div style="grid-column: span 2; display:flex; flex-direction:column; align-items:center;">

                        <!-- Step indicator (1 of 3 as shown in screenshot) -->
                        <div class="step-track" aria-hidden="true">
                            <div class="step step-1 active"><span>1</span></div>
                            <div class="bar"></div>
                            <div class="step step-2"><span>2</span></div>
                            <div class="bar"></div>
                            <div class="step step-3"><span>3</span></div>
                        </div>

                        <h2 class="book-title">Choose what service you want to book</h2>

                        <form id="serviceForm" action="bookservicestep2.php" method="GET"
                            style="width:100%; max-width:900px;">
                            <input type="hidden" name="service" id="serviceInput" value="">

                            <div class="service-grid">
                                <button type="button" class="service-btn" data-service="Assembly">Assembly</button>
                                <button type="button" class="service-btn" data-service="Cleaning">Cleaning</button>
                                <button type="button" class="service-btn" data-service="Mounting">Mounting</button>
                                <button type="button" class="service-btn" data-service="Outdoor Repair">Outdoor
                                    Repair</button>
                                <button type="button" class="service-btn" data-service="Moving">Moving</button>
                                <button type="button" class="service-btn" data-service="Plumbing">Plumbing</button>
                                <button type="button" class="service-btn" data-service="Painting">Painting</button>
                                <button type="button" class="service-btn" data-service="Pest Control">Pest
                                    Control</button>
                            </div>

                            <div style="margin-top:28px; text-align:center;">
                                <button id="proceedBtn" class="btn proceed" type="submit" disabled>Proceed</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        (function () { const btn = document.getElementById('profileBtn'); const dd = document.getElementById('profileDropdown'); btn && btn.addEventListener('click', e => { e.stopPropagation(); dd.classList.toggle('open'); }); document.addEventListener('click', e => { if (!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.remove('open'); }); })();

        // service selection behavior
        (function () {
            const buttons = document.querySelectorAll('.service-btn');
            const input = document.getElementById('serviceInput');
            const proceed = document.getElementById('proceedBtn');

            buttons.forEach(b => b.addEventListener('click', () => {
                // clear others
                buttons.forEach(x => x.classList.remove('active'));
                b.classList.add('active');
                input.value = b.dataset.service || '';
                proceed.disabled = !input.value;
                // keep focus for keyboard accessibility
                b.focus();
            }));

            // allow keyboard selection with Enter key
            buttons.forEach(b => b.addEventListener('keyup', (e) => {
                if (e.key === 'Enter' || e.key === ' ') b.click();
            }));

            // If user comes with ?service= param preselected, mark it
            const urlParams = new URLSearchParams(window.location.search);
            const preset = urlParams.get('service');
            if (preset) {
                const presetBtn = Array.from(buttons).find(x => x.dataset.service === preset);
                if (presetBtn) presetBtn.click();
            }
        })();

    </script>
</body>

</html>