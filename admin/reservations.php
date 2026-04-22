<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

$success = '';

// Confirm reservation
if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    $conn->query("UPDATE reservations SET status='confirmed' WHERE id=$id");
    $success = "Reservation confirmed successfully!";
}

// Cancel reservation
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $conn->query("UPDATE reservations SET status='cancelled' WHERE id=$id");
    $success = "Reservation cancelled.";
}

// Delete reservation
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reservations WHERE id=$id");
    header("Location: reservations.php?deleted=1");
    exit();
}
if (isset($_GET['deleted'])) $success = "Reservation deleted.";

// Filter
$status_filter = $_GET['status'] ?? 'all';
$where = $status_filter !== 'all' ? "WHERE status='$status_filter'" : '';
$reservations = $conn->query("SELECT * FROM reservations $where ORDER BY date DESC, created_at DESC");

// Counts
$total     = $conn->query("SELECT COUNT(*) as c FROM reservations")->fetch_assoc()['c'];
$pending   = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='pending'")->fetch_assoc()['c'];
$confirmed = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='confirmed'")->fetch_assoc()['c'];
$cancelled = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='cancelled'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reservations — DineEase Admin</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; color:#333; }
    .sidebar {
      position:fixed; top:0; left:0; width:240px; height:100vh;
      background:#1a1208; padding:30px 0; z-index:100;
    }
    .sidebar-logo {
      font-size:22px; font-weight:700; color:#c8913a;
      text-align:center; padding:0 20px 28px;
      border-bottom:1px solid #2e2010; font-style:italic;
    }
    .sidebar-logo span { font-size:12px; color:#8a7060; display:block; font-weight:400; font-style:normal; }
    .nav-item {
      display:flex; align-items:center; gap:10px;
      padding:14px 24px; color:#8a7060; text-decoration:none;
      font-size:14px; transition:all 0.2s; border-left:3px solid transparent;
    }
    .nav-item:hover, .nav-item.active { color:#c8913a; background:rgba(200,145,58,0.08); border-left-color:#c8913a; }
    .badge { background:#e74c3c; color:#fff; font-size:10px; padding:2px 7px; border-radius:20px; font-weight:700; }
    .nav-logout { position:absolute; bottom:20px; width:100%; }
    .main { margin-left:240px; padding:32px; }
    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
    .page-header h1 { font-size:24px; color:#1a1208; }
    .alert { padding:12px 18px; border-radius:6px; margin-bottom:20px; font-size:14px; }
    .alert.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:24px; }
    .stat-card {
      background:#fff; padding:18px 22px; border-radius:10px;
      border-left:4px solid #c8913a; box-shadow:0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-card .num { font-size:30px; font-weight:800; color:#1a1208; }
    .stat-card .label { font-size:12px; color:#999; margin-top:3px; }
    .filter-tabs { display:flex; gap:8px; margin-bottom:18px; }
    .filter-tab {
      padding:7px 18px; border-radius:20px; border:1px solid #ddd;
      background:#fff; font-size:12px; cursor:pointer;
      text-decoration:none; color:#555; transition:all 0.2s; font-weight:600;
    }
    .filter-tab:hover, .filter-tab.active { background:#c8913a; color:#fff; border-color:#c8913a; }
    .table-wrap { background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.06); }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:#1a1208; }
    thead th { padding:13px 16px; text-align:left; font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#c8913a; }
    tbody tr { border-bottom:1px solid #f5f5f5; transition:background 0.15s; }
    tbody tr:hover { background:#fffbf5; }
    td { padding:13px 16px; font-size:14px; }
    .status-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
    .status-pending   { background:#fff3cd; color:#856404; }
    .status-confirmed { background:#d4edda; color:#155724; }
    .status-cancelled { background:#f8d7da; color:#721c24; }
    .actions { display:flex; gap:7px; }
    .btn-sm {
      padding:5px 13px; border:none; border-radius:4px;
      font-size:12px; font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block; transition:opacity 0.2s;
    }
    .btn-sm:hover { opacity:0.8; }
    .btn-confirm  { background:#27ae60; color:#fff; }
    .btn-cancel   { background:#f39c12; color:#fff; }
    .btn-delete   { background:#e74c3c; color:#fff; }
    .guest-note { font-size:12px; color:#aaa; margin-top:2px; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-logo">DineEase <span>Admin Panel</span></div>
  <a href="dashboard.php"    class="nav-item">📊 Dashboard</a>
  <a href="reservations.php" class="nav-item active">
    📅 Reservations
    <?php if($pending > 0): ?><span class="badge"><?= $pending ?></span><?php endif; ?>
  </a>
  <a href="menu.php"     class="nav-item">🍽️ Menu Items</a>
  <a href="messages.php" class="nav-item">✉️ Messages</a>
  <a href="logout.php"   class="nav-item nav-logout">🚪 Logout</a>
</div>

<div class="main">
  <div class="page-header">
    <h1>📅 Reservations</h1>
    <span style="font-size:14px;color:#888;"><?= date('D, d M Y') ?></span>
  </div>

  <?php if ($success): ?>
    <div class="alert success">✅ <?= $success ?></div>
  <?php endif; ?>

  <div class="stats-row">
    <div class="stat-card">
      <div class="num"><?= $total ?></div>
      <div class="label">Total</div>
    </div>
    <div class="stat-card" style="border-left-color:#f39c12">
      <div class="num" style="color:#f39c12"><?= $pending ?></div>
      <div class="label">Pending</div>
    </div>
    <div class="stat-card" style="border-left-color:#27ae60">
      <div class="num" style="color:#27ae60"><?= $confirmed ?></div>
      <div class="label">Confirmed</div>
    </div>
    <div class="stat-card" style="border-left-color:#e74c3c">
      <div class="num" style="color:#e74c3c"><?= $cancelled ?></div>
      <div class="label">Cancelled</div>
    </div>
  </div>

  <div class="filter-tabs">
    <a href="?status=all"       class="filter-tab <?= $status_filter==='all'       ? 'active':'' ?>">All (<?= $total ?>)</a>
    <a href="?status=pending"   class="filter-tab <?= $status_filter==='pending'   ? 'active':'' ?>">⏳ Pending (<?= $pending ?>)</a>
    <a href="?status=confirmed" class="filter-tab <?= $status_filter==='confirmed' ? 'active':'' ?>">✅ Confirmed</a>
    <a href="?status=cancelled" class="filter-tab <?= $status_filter==='cancelled' ? 'active':'' ?>">❌ Cancelled</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Guest</th>
          <th>Date & Time</th>
          <th>Guests</th>
          <th>Message</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($reservations->num_rows === 0): ?>
          <tr>
            <td colspan="7" style="text-align:center;padding:40px;color:#bbb;">No reservations found.</td>
          </tr>
        <?php else: ?>
          <?php while ($row = $reservations->fetch_assoc()): ?>
            <tr>
              <td style="color:#aaa;font-size:12px;">#<?= $row['id'] ?></td>
              <td>
                <strong><?= htmlspecialchars($row['name']) ?></strong>
                <div class="guest-note">
                  <?= htmlspecialchars($row['phone']) ?>
                  <?php if($row['email']): ?> · <?= htmlspecialchars($row['email']) ?><?php endif; ?>
                </div>
              </td>
              <td>
                <strong><?= date('d M Y', strtotime($row['date'])) ?></strong>
                <div class="guest-note"><?= htmlspecialchars($row['time']) ?></div>
              </td>
              <td><?= $row['guests'] ?> 👥</td>
              <td style="max-width:180px;font-size:13px;color:#777;">
                <?= $row['message'] ? htmlspecialchars(substr($row['message'],0,50)).'...' : '—' ?>
              </td>
              <td>
                <span class="status-badge status-<?= $row['status'] ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <?php if ($row['status'] === 'pending'): ?>
                    <a href="?confirm=<?= $row['id'] ?>" class="btn-sm btn-confirm">✔ Confirm</a>
                    <a href="?cancel=<?= $row['id'] ?>"  class="btn-sm btn-cancel">✖ Cancel</a>
                  <?php endif; ?>
                  <a href="?delete=<?= $row['id'] ?>"
                     onclick="return confirm('Delete this reservation?')"
                     class="btn-sm btn-delete">🗑</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>