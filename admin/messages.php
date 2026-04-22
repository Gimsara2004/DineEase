<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

// Mark as read when opened
if (isset($_GET['read'])) {
    $id = intval($_GET['read']);
    $conn->query("UPDATE messages SET is_read = 1 WHERE id = $id");
    header("Location: messages.php");
    exit();
}

// Delete message
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit();
}

// Get all messages
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
$unread   = $conn->query("SELECT COUNT(*) as cnt FROM messages WHERE is_read = 0")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages — Admin</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', sans-serif; background:#f4f6f9; color:#333; }

    /* Sidebar */
    .sidebar {
      position: fixed; top:0; left:0;
      width: 240px; height:100vh;
      background: #1a1208;
      padding: 30px 0;
    }
    .sidebar-logo {
      font-size:22px; font-weight:700;
      color:#c8913a; text-align:center;
      padding: 0 20px 28px;
      border-bottom: 1px solid #2e2010;
    }
    .sidebar-logo span { font-size:12px; color:#8a7060; display:block; font-weight:400; }
    .nav-item {
      display:block; padding:14px 24px;
      color:#8a7060; text-decoration:none;
      font-size:14px; transition:all 0.2s;
      border-left: 3px solid transparent;
    }
    .nav-item:hover, .nav-item.active {
      color:#c8913a; background:rgba(200,145,58,0.08);
      border-left-color:#c8913a;
    }
    .nav-item .icon { margin-right:10px; }

    /* Main */
    .main { margin-left:240px; padding:32px; }

    .page-header {
      display:flex; justify-content:space-between;
      align-items:center; margin-bottom:28px;
    }
    .page-header h1 { font-size:24px; color:#1a1208; }
    .page-header h1 span {
      font-size:13px; background:#e74c3c;
      color:#fff; padding:3px 10px;
      border-radius:20px; margin-left:10px;
      vertical-align:middle;
    }

    /* Stats */
    .stats-row {
      display:grid; grid-template-columns:repeat(3,1fr);
      gap:20px; margin-bottom:28px;
    }
    .stat-card {
      background:#fff; padding:20px 24px;
      border-radius:8px;
      border-left:4px solid #c8913a;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-card .num { font-size:32px; font-weight:700; color:#1a1208; }
    .stat-card .label { font-size:12px; color:#888; margin-top:4px; }

    /* Messages list */
    .messages-list { display:flex; flex-direction:column; gap:12px; }

    .message-card {
      background:#fff;
      border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      overflow:hidden;
      transition:transform 0.2s;
    }
    .message-card:hover { transform:translateY(-2px); }
    .message-card.unread { border-left:4px solid #c8913a; }
    .message-card.read   { border-left:4px solid #ddd; }

    .message-header {
      display:flex; justify-content:space-between;
      align-items:center;
      padding:16px 20px;
      cursor:pointer;
      background:#fafafa;
    }
    .message-card.unread .message-header { background:#fffbf5; }

    .sender-info { display:flex; align-items:center; gap:14px; }
    .avatar {
      width:40px; height:40px; border-radius:50%;
      background:#c8913a; color:#fff;
      display:flex; align-items:center; justify-content:center;
      font-weight:700; font-size:16px;
    }
    .message-card.read .avatar { background:#ccc; }
    .sender-name { font-weight:600; font-size:15px; }
    .sender-contact { font-size:12px; color:#888; margin-top:2px; }

    .message-meta {
      display:flex; align-items:center; gap:12px;
    }
    .unread-badge {
      background:#c8913a; color:#fff;
      font-size:10px; padding:3px 10px;
      border-radius:20px; font-weight:700;
      letter-spacing:1px;
    }
    .message-date { font-size:12px; color:#aaa; }

    .message-body {
      padding:0 20px;
      max-height:0; overflow:hidden;
      transition:max-height 0.3s ease, padding 0.3s;
    }
    .message-body.open {
      max-height:300px;
      padding:16px 20px;
    }
    .message-body p {
      font-size:14px; color:#555;
      line-height:1.7;
      background:#f8f8f8;
      padding:14px; border-radius:6px;
      margin-bottom:14px;
    }
    .message-actions { display:flex; gap:10px; padding-bottom:4px; }
    .btn-action {
      padding:7px 18px; border:none;
      border-radius:4px; cursor:pointer;
      font-size:12px; font-weight:600;
      text-decoration:none; display:inline-block;
      transition:opacity 0.2s;
    }
    .btn-action:hover { opacity:0.8; }
    .btn-reply  { background:#c8913a; color:#fff; }
    .btn-read   { background:#27ae60; color:#fff; }
    .btn-delete { background:#e74c3c; color:#fff; }

    /* Empty state */
    .empty-state {
      text-align:center; padding:60px 20px;
      color:#aaa;
    }
    .empty-state .icon { font-size:60px; margin-bottom:16px; }
    .empty-state p { font-size:16px; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    DineEase <span>Admin Panel</span>
  </div>
  <a href="dashboard.php"  class="nav-item"><span class="icon">📊</span> Dashboard</a>
  <a href="reservations.php" class="nav-item"><span class="icon">📅</span> Reservations</a>
  <a href="menu.php"       class="nav-item"><span class="icon">🍽️</span> Menu Items</a>
  <a href="messages.php"   class="nav-item active"><span class="icon">✉️</span> Messages</a>
  <a href="logout.php"     class="nav-item" style="margin-top:auto; position:absolute; bottom:20px; width:100%;">
    <span class="icon">🚪</span> Logout
  </a>
</div>

<!-- Main -->
<div class="main">

  <div class="page-header">
    <h1>Messages <?php if($unread > 0): ?>
      <span><?= $unread ?> New</span>
    <?php endif; ?></h1>
  </div>

  <!-- Stats -->
  <?php
    $total  = $conn->query("SELECT COUNT(*) as c FROM messages")->fetch_assoc()['c'];
    $read   = $conn->query("SELECT COUNT(*) as c FROM messages WHERE is_read=1")->fetch_assoc()['c'];
  ?>
  <div class="stats-row">
    <div class="stat-card">
      <div class="num"><?= $total ?></div>
      <div class="label">Total Messages</div>
    </div>
    <div class="stat-card">
      <div class="num" style="color:#c8913a"><?= $unread ?></div>
      <div class="label">Unread Messages</div>
    </div>
    <div class="stat-card">
      <div class="num" style="color:#27ae60"><?= $read ?></div>
      <div class="label">Read Messages</div>
    </div>
  </div>

  <!-- Messages -->
  <div class="messages-list">
    <?php if ($messages->num_rows === 0): ?>
      <div class="empty-state">
        <div class="icon">📭</div>
        <p>No messages yet.</p>
      </div>
    <?php else: ?>
      <?php while ($msg = $messages->fetch_assoc()): ?>
        <div class="message-card <?= $msg['is_read'] ? 'read' : 'unread' ?>" id="msg-<?= $msg['id'] ?>">
          <div class="message-header" onclick="toggleMsg(<?= $msg['id'] ?>)">
            <div class="sender-info">
              <div class="avatar"><?= strtoupper(substr($msg['name'], 0, 1)) ?></div>
              <div>
                <div class="sender-name"><?= htmlspecialchars($msg['name']) ?></div>
                <div class="sender-contact">
                  <?= htmlspecialchars($msg['email']) ?>
                  <?php if($msg['phone']): ?> · <?= htmlspecialchars($msg['phone']) ?><?php endif; ?>
                </div>
              </div>
            </div>
            <div class="message-meta">
              <?php if (!$msg['is_read']): ?>
                <span class="unread-badge">NEW</span>
              <?php endif; ?>
              <span class="message-date">
                <?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?>
              </span>
            </div>
          </div>

          <div class="message-body" id="body-<?= $msg['id'] ?>">
            <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
            <div class="message-actions">
              <a href="mailto:<?= $msg['email'] ?>" class="btn-action btn-reply">✉ Reply by Email</a>
              <?php if (!$msg['is_read']): ?>
                <a href="?read=<?= $msg['id'] ?>" class="btn-action btn-read">✔ Mark as Read</a>
              <?php endif; ?>
              <a href="?delete=<?= $msg['id'] ?>"
                 onclick="return confirm('Delete this message?')"
                 class="btn-action btn-delete">🗑 Delete</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</div>

<script>
function toggleMsg(id) {
  const body = document.getElementById('body-' + id);
  body.classList.toggle('open');
}
</script>
</body>
</html>