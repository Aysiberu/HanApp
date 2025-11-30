<?php
session_start();
include 'supabase.php';

// simple handling: pick service from GET, show confirm and allow back/next
$service = isset($_GET['service']) ? trim($_GET['service']) : '';
if (!$service) {
    header('Location: bookservice.php');
    exit;
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'You';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HanApp — Book Service — Step 2</title>
    <link rel="stylesheet" href="accountinfo.css">
</head>

<body>
    <header>
        <div class="brand"><img src="image/Logo.png" alt="HanApp"></div>
        <div class="user">
            <button id="profileBtn" class="profile-btn"><?php echo htmlspecialchars($user_name); ?><span
                    class="caret">▾</span></button>
            <div id="profileDropdown" class="profile-dropdown">
                <ul>
                    <li><a href="bookservice.php">Book Service</a></li>
                    <li><a href="accountinfo.php">Account Info</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <div class="nav-card">
                <nav>
                    <ul class="nav">
                        <li><a href="bookservice.php" class="active">Book Service</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="main">
            <div class="card" style="text-align:center;padding:40px;">
                <div style="font-weight:700; color:var(--accent); font-size:20px;">Step 2 — Provide details</div>
                <p style="margin-top:14px">You chose: <strong><?php echo htmlspecialchars($service); ?></strong></p>

                <div style="margin-top:20px; display:flex; gap:12px; justify-content:center;">
                    <a class="btn" href="bookservice.php">← Back</a>
                    <a class="btn" href="bookinghistory.php">Confirm & Continue</a>
                </div>

            </div>
        </main>
    </div>

    <script>
        (function () { const btn = document.getElementById('profileBtn'), dd = document.getElementById('profileDropdown'); btn && btn.addEventListener('click', e => { e.stopPropagation(); dd.classList.toggle('open'); }); document.addEventListener('click', e => { if (!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.remove('open'); }); })();
    </script>
</body>

</html>