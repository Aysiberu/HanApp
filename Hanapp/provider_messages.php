<?php
session_start();
require 'supabase.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php');
    exit;
}

$provider_id = (int)$_SESSION['user_id'];
$to = isset($_GET['to']) ? (int)$_GET['to'] : null;

// Fetch all unique users who have conversations with this provider
$messages = sb_select('messages', sprintf('select=sender_id,receiver_id&or=(sender_id.eq.%d,receiver_id.eq.%d)', $provider_id, $provider_id));

$partners = [];
$seen = [];
if (!empty($messages)) {
  foreach ($messages as $m) {
    $other = ((int)$m['sender_id'] === $provider_id) ? (int)$m['receiver_id'] : (int)$m['sender_id'];
    if ($other === 0 || isset($seen[$other])) continue;
    $seen[$other] = true;
    $u = sb_getById('users', $other);
    if ($u) $partners[] = $u;
  }
}

if (!$to && count($partners) > 0) $to = (int)$partners[0]['id'];

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>HanApp — Provider Messages</title>
    <link rel="stylesheet" href="accountinfo.css">
    <link rel="stylesheet" href="messages.css">
  </head>
  <body>
    <header>
      <div class="brand"><img src="assets/logo.png" alt="HanApp"></div>
      <div class="user">
        <button id="profileBtn" class="profile-btn">
          <span style="font-size:14px; color:#333"><?php echo htmlspecialchars(
              isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Provider'
          ); ?></span>
          <span class="avatar"><img src="" alt="user"></span>
          <span class="caret">▾</span>
        </button>
        <div id="profileDropdown" class="profile-dropdown">
          <ul>
            <li><a href="bookservice.php">Book Service</a></li>
            <li><a href="accountinfo.php">Account Info</a></li>
            <li><a href="provider_messages.php">Inbox</a></li>
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
              <li><a href="provider_messages.php" class="active">Inbox</a></li>
              <li><a href="settings.php">Settings</a></li>
            </ul>
          </nav>
        </div>
      </aside>

      <main class="main">
        <div class="card chat-card">
          <div class="chat-wrap">
            <div class="conversations">
              <div class="conv-header">
                <h3>Inbox</h3>
                <small>Customers</small>
              </div>
              <div class="conv-list">
                <?php if (count($partners) === 0): ?>
                  <div class="no-conv">No messages yet.</div>
                <?php else: ?>
                  <?php foreach ($partners as $p): ?>
                    <a class="conv-item <?php echo ($to && $to == $p['id']) ? 'active' : ''; ?>" href="?to=<?php echo $p['id']; ?>">
                      <div class="avatar-mini"></div>
                      <div class="meta">
                        <strong><?php echo htmlspecialchars($p['first_name'].' '.$p['last_name']); ?></strong>
                        <span><?php echo htmlspecialchars($p['email']); ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="chat-area">
              <?php if (!$to): ?>
                <div class="empty-chat">Select a conversation to begin.</div>
                <?php else:
                $partner = sb_getById('users',$to);
              ?>
              <div class="chat-top">
                <div class="avatar-large"></div>
                <div class="chat-title"><strong><?php echo htmlspecialchars($partner['first_name'].' '.$partner['last_name']); ?></strong>
                  <span><?php echo htmlspecialchars($partner['email']); ?></span>
                </div>
              </div>

              <div id="messages" class="messages" data-user="<?php echo $provider_id; ?>" data-to="<?php echo $to; ?>"></div>

              <form id="sendForm" class="chat-input" onsubmit="return sendMessage();">
                <textarea id="msgText" placeholder="Type a message…" required></textarea>
                <button type="submit" class="send">Send</button>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </main>
    </div>

    <script>
      // reuse same small JS used in messages.php (polling)
      (function(){
        const btn = document.getElementById('profileBtn');
        const dd = document.getElementById('profileDropdown');
        btn && btn.addEventListener('click', function(e){ e.stopPropagation(); dd.classList.toggle('open'); });
        document.addEventListener('click', function(e){ if(!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.remove('open'); });
      })();

      let pollTimer;
      function appendMessageNode(m){
        const div = document.createElement('div');
        div.className='msg '+(parseInt(m.sender_id)===<?php echo $provider_id; ?> ? 'sent':'recv');
        div.innerHTML = '<div class="bubble">'+m.message+'<div class="time">'+m.created_at+'</div></div>';
        return div;
      }

      function loadMessages(){
        const box = document.getElementById('messages'); if(!box) return;
        const user = box.dataset.user; const to = box.dataset.to;
        fetch('fetch_messages.php?u='+user+'&other='+to).then(r=>r.json()).then(data=>{ box.innerHTML=''; data.forEach(m=>box.appendChild(appendMessageNode(m))); box.scrollTop = box.scrollHeight; }).catch(console.error);
      }

      function sendMessage(){
        const ta = document.getElementById('msgText'); if(!ta || ta.value.trim()==='') return false;
        const payload = new URLSearchParams(); payload.append('sender', <?php echo $provider_id; ?>); payload.append('receiver', <?php echo $to; ?>); payload.append('message', ta.value.trim());
        fetch('send_message.php',{method:'POST', body:payload}).then(r=>r.json()).then(resp=>{ if(resp.success){ ta.value=''; loadMessages(); } }).catch(console.error);
        return false;
      }

      if(document.getElementById('messages')){ loadMessages(); pollTimer = setInterval(loadMessages, 2000); }
    </script>

  </body>
</html>
