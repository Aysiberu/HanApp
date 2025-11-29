<?php
session_start();
require 'supabase.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php'); exit;
}

$service = isset($_GET['service']) ? trim($_GET['service']) : '';
// filters
$time_of_day = isset($_GET['time_of_day']) ? $_GET['time_of_day'] : '';
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// safe default
if (!$service) {
    echo "<p>No service selected — go back to <a href='bookservice.php'>Book service</a>.</p>";
    exit;
}

// Fetch providers who offer the chosen service (PostgREST ilike used for case-insensitive match)
$pattern = '%' . $service . '%';
$query = 'select=*&service_types=ilike.' . rawurlencode($pattern) . '&order=rating.desc';
$providers = sb_select('providers', $query);
if (!$providers) $providers = [];

// apply simple time filters in PHP — interpret availability_from/to
function matches_time_filter($provider, $time_of_day, $from, $to) {
    if (!$time_of_day && !$from && !$to) return true;
    $start = isset($provider['availability_from']) ? $provider['availability_from'] : null;
    $end = isset($provider['availability_to']) ? $provider['availability_to'] : null;
    if (!$start || !$end) return true; // can't decide — show it

    // convert to seconds for comparison
    $s = strtotime($start);
    $e = strtotime($end);

    if ($time_of_day === 'morning') {
        return ($s <= strtotime('12:00') && $e >= strtotime('08:00'));
    }
    if ($time_of_day === 'afternoon') {
        return ($s <= strtotime('17:00') && $e >= strtotime('12:00'));
    }
    if ($time_of_day === 'evening') {
        return ($s <= strtotime('21:30') && $e >= strtotime('17:00'));
    }

    if ($from || $to) {
        $f = $from ? strtotime($from) : null;
        $t = $to ? strtotime($to) : null;
        if ($f && $t) {
            return ($s <= $t && $e >= $f);
        }
        if ($f) return ($e >= $f);
        if ($t) return ($s <= $t);
    }

    return true;
}

// filter list
$filtered = array_values(array_filter($providers, function($p) use ($time_of_day,$from,$to){ return matches_time_filter($p,$time_of_day,$from,$to); }));

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>HanApp — Choose your Tasker</title>
    <link rel="stylesheet" href="accountinfo.css">
    <link rel="stylesheet" href="bookservicestep2.css">
  </head>
  <body>
    <header>
      <div class="brand"><img src="assets/logo.png" alt="HanApp"></div>
      <div class="user">
        <button id="profileBtn" class="profile-btn"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'You'); ?><span class="caret">▾</span></button>
        <div id="profileDropdown" class="profile-dropdown"><ul><li><a href="bookservice.php">Book Service</a></li><li><a href="accountinfo.php">Account Info</a></li></ul></div>
      </div>
    </header>

    <div class="wide-banner">Use the filter to find your Tasker. Change the date and time according to your preference.</div>

    <div class="layout large-layout">
      <aside class="sidebar">
        <div class="nav-card filter-card">
          <h4>FILTER</h4>
          <form method="GET" action="bookservicestep2.php">
            <input type="hidden" name="service" value="<?php echo htmlspecialchars($service); ?>">

            <label>Date</label>
            <div class="date-row">
              <button name="date_filter" value="today" type="submit" class="small">Today</button>
              <button name="date_filter" value="3days" type="submit" class="small">Within 3 Days</button>
              <button name="date_filter" value="week" type="submit" class="small">Within a Week</button>
            </div>

            <div style="margin-top:12px;">
              <label>Time of Day</label>
              <div style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                <label><input type="radio" name="time_of_day" value="morning" <?php echo $time_of_day === 'morning' ? 'checked' : '';?> /> Morning (8AM - 12PM)</label>
                <label><input type="radio" name="time_of_day" value="afternoon" <?php echo $time_of_day === 'afternoon' ? 'checked' : '';?> /> Afternoon (12PM - 5PM)</label>
                <label><input type="radio" name="time_of_day" value="evening" <?php echo $time_of_day === 'evening' ? 'checked' : '';?> /> Evening (5PM - 9:30PM)</label>
              </div>
            </div>

            <div style="margin-top:12px;">
              <label>From/To</label>
              <div style="display:flex; gap:8px; margin-top:6px; align-items:center;">
                <input type="time" name="from" value="<?php echo htmlspecialchars($from); ?>" />
                <span style="color:var(--muted);">to</span>
                <input type="time" name="to" value="<?php echo htmlspecialchars($to); ?>" />
              </div>
            </div>

            <div style="margin-top:14px;">
              <button class="btn" type="submit">Apply Filters</button>
            </div>
          </form>
        </div>
      </aside>

      <main class="main">
        <div class="card">
          <div class="section-header">
            <div class="tabs"><div class="tab">Select your Tasker</div></div>
            <div class="section-title">RECOMMENDED TASKERS NEAR YOU</div>
          </div>

          <div class="tasker-list">
            <?php if (count($filtered) == 0): ?>
              <p>No providers found for "<?php echo htmlspecialchars($service); ?>" with your current filters.</p>
            <?php else: ?>
              <?php foreach ($filtered as $p): ?>
                <div class="tasker-card">
                  <div class="left-block">
                    <div class="profile-photo"><img src="<?php echo htmlspecialchars($p['photo'] ?: 'https://i.pravatar.cc/120'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>"></div>
                    <div class="select-wrap">
                      <a class="link small" href="#">View Profile and Reviews</a>
                      <a class="btn select-btn" href="bookservice_step3.php?provider=<?php echo $p['id']; ?>&service=<?php echo urlencode($service); ?>">Select and Continue</a>
                    </div>
                  </div>

                  <div class="right-block">
                    <div class="header-row">
                      <div class="title">
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <div class="meta small muted"><?php echo htmlspecialchars($p['location']); ?></div>
                      </div>
                      <div class="price"><?php echo htmlspecialchars($p['price_per_hour']); ?></div>
                    </div>

                    <div class="info-row small muted">
                      <div><span class="icon">⛳</span> <?php echo htmlspecialchars($p['completed_tasks']); ?> Completed <?php echo htmlspecialchars($service); ?> task</div>
                      <div><span class="icon">🕒</span> <?php echo htmlspecialchars($p['availability']); ?></div>
                      <div><span class="icon">⭐</span> <?php echo htmlspecialchars(number_format($p['rating'],2)); ?>/5.0</div>
                    </div>

                    <div class="bio">
                      <h4>How can I help:</h4>
                      <p><?php echo htmlspecialchars($p['bio'] ?: 'No bio yet.'); ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        </div>
      </main>
    </div>

  <script>
  (function(){ const btn = document.getElementById('profileBtn'); const dd = document.getElementById('profileDropdown'); btn && btn.addEventListener('click', e=>{ e.stopPropagation(); dd.classList.toggle('open'); }); document.addEventListener('click', e=>{ if(!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.remove('open'); }); })();
  </script>

  </body>
</html>
