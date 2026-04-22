<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

$success = '';
$error   = '';

// ── ADD new item ────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name        = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price       = floatval($_POST['price']);
    $category    = $conn->real_escape_string($_POST['category']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle image upload
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../uploads/menu/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(100,999) . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image = $filename;
        }
    }

    $sql = "INSERT INTO menu_items 
            (name, description, price, category, image, is_featured, is_available)
            VALUES ('$name','$description',$price,'$category','$image',$is_featured,1)";

    if ($conn->query($sql)) {
        $success = 'Menu item added successfully!';
    } else {
        $error = 'Failed to add item. Try again.';
    }
}

// ── DELETE item ──────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM menu_items WHERE id = $id");
    header("Location: menu.php?deleted=1");
    exit();
}

// ── TOGGLE availability ──────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $id   = intval($_GET['toggle']);
    $item = $conn->query("SELECT is_available FROM menu_items WHERE id=$id")->fetch_assoc();
    $new  = $item['is_available'] ? 0 : 1;
    $conn->query("UPDATE menu_items SET is_available=$new WHERE id=$id");
    header("Location: menu.php");
    exit();
}

// ── TOGGLE featured ──────────────────────────────────────────────────────────
if (isset($_GET['feature'])) {
    $id   = intval($_GET['feature']);
    $item = $conn->query("SELECT is_featured FROM menu_items WHERE id=$id")->fetch_assoc();
    $new  = $item['is_featured'] ? 0 : 1;
    $conn->query("UPDATE menu_items SET is_featured=$new WHERE id=$id");
    header("Location: menu.php");
    exit();
}

if (isset($_GET['deleted'])) $success = 'Item deleted successfully!';

// ── GET all items ────────────────────────────────────────────────────────────
$filter   = $_GET['cat'] ?? 'all';
$where    = $filter !== 'all' ? "WHERE category='$filter'" : '';
$items    = $conn->query("SELECT * FROM menu_items $where ORDER BY category, name");
$total    = $conn->query("SELECT COUNT(*) as c FROM menu_items")->fetch_assoc()['c'];
$available= $conn->query("SELECT COUNT(*) as c FROM menu_items WHERE is_available=1")->fetch_assoc()['c'];
$featured = $conn->query("SELECT COUNT(*) as c FROM menu_items WHERE is_featured=1")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu Management — Admin</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; color:#333; }

    /* Sidebar */
    .sidebar {
      position:fixed; top:0; left:0;
      width:240px; height:100vh;
      background:#1a1208; padding:30px 0;
    }
    .sidebar-logo {
      font-size:22px; font-weight:700; color:#c8913a;
      text-align:center; padding:0 20px 28px;
      border-bottom:1px solid #2e2010;
    }
    .sidebar-logo span { font-size:12px; color:#8a7060; display:block; font-weight:400; }
    .nav-item {
      display:block; padding:14px 24px; color:#8a7060;
      text-decoration:none; font-size:14px;
      transition:all 0.2s; border-left:3px solid transparent;
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

    .btn-open-form {
      background:#c8913a; color:#fff;
      border:none; padding:10px 24px;
      font-size:14px; font-weight:600;
      cursor:pointer; border-radius:6px;
      transition:background 0.2s;
    }
    .btn-open-form:hover { background:#a87030; }

    /* Alert */
    .alert {
      padding:12px 18px; border-radius:6px;
      margin-bottom:20px; font-size:14px;
    }
    .alert.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .alert.error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

    /* Stats */
    .stats-row {
      display:grid; grid-template-columns:repeat(3,1fr);
      gap:20px; margin-bottom:28px;
    }
    .stat-card {
      background:#fff; padding:20px 24px;
      border-radius:8px; border-left:4px solid #c8913a;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-card .num { font-size:32px; font-weight:700; color:#1a1208; }
    .stat-card .label { font-size:12px; color:#888; margin-top:4px; }

    /* Filter tabs */
    .filter-tabs { display:flex; gap:6px; margin-bottom:20px; flex-wrap:wrap; }
    .filter-tab {
      padding:7px 18px; border-radius:20px;
      border:1px solid #ddd; background:#fff;
      font-size:12px; cursor:pointer;
      text-decoration:none; color:#555;
      transition:all 0.2s;
    }
    .filter-tab:hover, .filter-tab.active {
      background:#c8913a; color:#fff; border-color:#c8913a;
    }

    /* Add Item Form */
    .add-form {
      background:#fff; border-radius:8px;
      padding:28px; margin-bottom:28px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      display:none;
    }
    .add-form.open { display:block; }
    .add-form h2 { font-size:18px; color:#1a1208; margin-bottom:20px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group.full { grid-column:span 2; }
    .form-group label { font-size:12px; font-weight:600; color:#555; letter-spacing:0.5px; }
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding:10px 14px; border:1px solid #ddd;
      border-radius:6px; font-size:14px;
      font-family:'Segoe UI',sans-serif;
      outline:none; transition:border-color 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color:#c8913a; }
    .form-group textarea { resize:vertical; min-height:80px; }
    .checkbox-row { display:flex; align-items:center; gap:8px; }
    .checkbox-row input[type=checkbox] { width:16px; height:16px; accent-color:#c8913a; }
    .form-actions { display:flex; gap:12px; margin-top:20px; }
    .btn-submit {
      background:#c8913a; color:#fff;
      border:none; padding:11px 28px;
      font-size:14px; font-weight:600;
      cursor:pointer; border-radius:6px;
    }
    .btn-cancel {
      background:#f0f0f0; color:#555;
      border:none; padding:11px 22px;
      font-size:14px; cursor:pointer;
      border-radius:6px;
    }

    /* Table */
    .table-wrap {
      background:#fff; border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      overflow:hidden;
    }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:#1a1208; }
    thead th {
      padding:14px 18px; text-align:left;
      font-size:11px; letter-spacing:1px;
      text-transform:uppercase; color:#c8913a;
    }
    tbody tr { border-bottom:1px solid #f0f0f0; transition:background 0.15s; }
    tbody tr:hover { background:#fffbf5; }
    td { padding:14px 18px; font-size:14px; vertical-align:middle; }

    .item-name { font-weight:600; color:#1a1208; }
    .item-desc { font-size:12px; color:#999; margin-top:3px; max-width:250px; }

    .category-badge {
      display:inline-block; padding:3px 12px;
      border-radius:20px; font-size:11px;
      font-weight:600; letter-spacing:0.5px;
    }
    .cat-starters  { background:#fff3cd; color:#856404; }
    .cat-mains     { background:#d1ecf1; color:#0c5460; }
    .cat-desserts  { background:#f8d7da; color:#721c24; }
    .cat-drinks    { background:#d4edda; color:#155724; }

    .price { font-weight:700; color:#c8913a; font-size:15px; }

    .status-badge {
      display:inline-block; padding:4px 12px;
      border-radius:20px; font-size:11px; font-weight:600;
    }
    .status-on  { background:#d4edda; color:#155724; }
    .status-off { background:#f8d7da; color:#721c24; }

    .star { font-size:18px; cursor:pointer; }

    .actions { display:flex; gap:8px; }
    .btn-sm {
      padding:6px 14px; border:none; border-radius:4px;
      font-size:12px; font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block;
      transition:opacity 0.2s;
    }
    .btn-sm:hover { opacity:0.8; }
    .btn-toggle  { background:#17a2b8; color:#fff; }
    .btn-edit    { background:#f0a500; color:#fff; }
    .btn-del     { background:#e74c3c; color:#fff; }

    .img-thumb {
      width:48px; height:48px; border-radius:6px;
      object-fit:cover; background:#f0f0f0;
    }
    .no-img {
      width:48px; height:48px; border-radius:6px;
      background:#f0f0f0; display:flex;
      align-items:center; justify-content:center;
      font-size:22px;
    }

    .empty-row td {
      text-align:center; padding:40px;
      color:#aaa; font-size:15px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">DineEase <span>Admin Panel</span></div>
  <a href="dashboard.php"    class="nav-item"><span class="icon">📊</span> Dashboard</a>
  <a href="reservations.php" class="nav-item"><span class="icon">📅</span> Reservations</a>
  <a href="menu.php"         class="nav-item active"><span class="icon">🍽️</span> Menu Items</a>
  <a href="messages.php"     class="nav-item"><span class="icon">✉️</span> Messages</a>
  <a href="logout.php"       class="nav-item" style="position:absolute;bottom:20px;width:100%;">
    <span class="icon">🚪</span> Logout
  </a>
</div>

<!-- Main -->
<div class="main">

  <div class="page-header">
    <h1>🍽️ Menu Management</h1>
    <button class="btn-open-form" onclick="toggleForm()">+ Add New Item</button>
  </div>

  <?php if ($success): ?>
    <div class="alert success">✅ <?= $success ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert error">❌ <?= $error ?></div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="num"><?= $total ?></div>
      <div class="label">Total Items</div>
    </div>
    <div class="stat-card">
      <div class="num" style="color:#27ae60"><?= $available ?></div>
      <div class="label">Available Items</div>
    </div>
    <div class="stat-card">
      <div class="num" style="color:#c8913a"><?= $featured ?></div>
      <div class="label">Featured Items</div>
    </div>
  </div>

  <!-- Add Form -->
  <div class="add-form" id="add-form">
    <h2>Add New Menu Item</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add"/>
      <div class="form-grid">
        <div class="form-group">
          <label>Item Name *</label>
          <input type="text" name="name" placeholder="e.g. Grilled Lobster" required/>
        </div>
        <div class="form-group">
          <label>Price (Rs.) *</label>
          <input type="number" name="price" step="0.01" placeholder="e.g. 3500" required/>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="starters">Starters</option>
            <option value="mains">Main Course</option>
            <option value="desserts">Desserts</option>
            <option value="drinks">Drinks</option>
          </select>
        </div>
        <div class="form-group">
          <label>Item Photo</label>
          <input type="file" name="image" accept="image/*"/>
        </div>
        <div class="form-group full">
          <label>Description</label>
          <textarea name="description" placeholder="Describe the dish..."></textarea>
        </div>
        <div class="form-group">
          <label>&nbsp;</label>
          <div class="checkbox-row">
            <input type="checkbox" name="is_featured" id="featured"/>
            <label for="featured" style="font-size:14px; font-weight:400;">Mark as Featured / Chef's Pick</label>
          </div>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-submit">✔ Add Item</button>
        <button type="button" class="btn-cancel" onclick="toggleForm()">Cancel</button>
      </div>
    </form>
  </div>

  <!-- Filter Tabs -->
  <div class="filter-tabs">
    <a href="?cat=all"      class="filter-tab <?= $filter==='all'      ? 'active':'' ?>">All (<?= $total ?>)</a>
    <a href="?cat=starters" class="filter-tab <?= $filter==='starters' ? 'active':'' ?>">🥗 Starters</a>
    <a href="?cat=mains"    class="filter-tab <?= $filter==='mains'    ? 'active':'' ?>">🍛 Mains</a>
    <a href="?cat=desserts" class="filter-tab <?= $filter==='desserts' ? 'active':'' ?>">🍮 Desserts</a>
    <a href="?cat=drinks"   class="filter-tab <?= $filter==='drinks'   ? 'active':'' ?>">🥤 Drinks</a>
  </div>

  <!-- Table -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Photo</th>
          <th>Item</th>
          <th>Category</th>
          <th>Price</th>
          <th>Status</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($items->num_rows === 0): ?>
          <tr class="empty-row">
            <td colspan="7">🍽️ No menu items found. Add your first item above!</td>
          </tr>
        <?php else: ?>
          <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
              <td>
                <?php if ($item['image']): ?>
                  <img src="../uploads/menu/<?= htmlspecialchars($item['image']) ?>"
                       class="img-thumb" alt=""/>
                <?php else: ?>
                  <div class="no-img">🍽️</div>
                <?php endif; ?>
              </td>
              <td>
                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                <div class="item-desc"><?= htmlspecialchars(substr($item['description'],0,60)) ?>...</div>
              </td>
              <td>
                <span class="category-badge cat-<?= $item['category'] ?>">
                  <?= ucfirst($item['category']) ?>
                </span>
              </td>
              <td><span class="price">Rs. <?= number_format($item['price'],2) ?></span></td>
              <td>
                <span class="status-badge <?= $item['is_available'] ? 'status-on':'status-off' ?>">
                  <?= $item['is_available'] ? '✔ Available' : '✖ Hidden' ?>
                </span>
              </td>
              <td>
                <a href="?feature=<?= $item['id'] ?>" class="star" title="Toggle featured">
                  <?= $item['is_featured'] ? '⭐' : '☆' ?>
                </a>
              </td>
              <td>
                <div class="actions">
                  <a href="?toggle=<?= $item['id'] ?>" class="btn-sm btn-toggle">
                    <?= $item['is_available'] ? 'Hide' : 'Show' ?>
                  </a>
                  <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                  <a href="?delete=<?= $item['id'] ?>"
                     onclick="return confirm('Delete <?= htmlspecialchars($item['name']) ?>?')"
                     class="btn-sm btn-del">Delete</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<script>
function toggleForm() {
  const form = document.getElementById('add-form');
  form.classList.toggle('open');
  if (form.classList.contains('open')) {
    form.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>
</body>
</html>

