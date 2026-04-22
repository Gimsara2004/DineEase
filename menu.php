<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu – The Golden Fork</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root { --gold:#c9a84c; --dark:#1a1a1a; --cream:#f9f5ef; }
        body { font-family:'Lato',sans-serif; background:var(--cream); }
        nav {
            background:var(--dark); padding:18px 40px;
            display:flex; justify-content:space-between; align-items:center;
        }
        .logo { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.5rem; }
        nav a { color:#fff; text-decoration:none; }
        nav a:hover { color:var(--gold); }

        .page-hero {
            background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),
                        url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1400') center/cover;
            height: 280px;
            display:flex; align-items:center; justify-content:center;
            flex-direction:column; text-align:center; color:#fff;
        }
        .page-hero h1 { font-family:'Playfair Display',serif; font-size:3rem; }
        .page-hero p  { color:var(--gold); letter-spacing:3px; margin-top:8px; }

        .tabs {
            display:flex; justify-content:center; gap:10px;
            padding:40px 20px 20px; flex-wrap:wrap;
        }
        .tab-btn {
            padding:10px 28px; border:2px solid var(--gold);
            background:transparent; color:var(--gold);
            cursor:pointer; font-size:0.85rem; letter-spacing:2px;
            text-transform:uppercase; transition:all 0.3s;
        }
        .tab-btn.active, .tab-btn:hover {
            background:var(--gold); color:#fff;
        }

        .menu-grid {
            display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:24px; max-width:1100px; margin:0 auto; padding:20px 40px 60px;
        }
        .menu-card {
            background:#fff; padding:28px;
            border-left:4px solid var(--gold);
            display:flex; flex-direction:column;
        }
        .menu-card .tag {
            font-size:0.72rem; color:var(--gold);
            text-transform:uppercase; letter-spacing:2px; margin-bottom:8px;
        }
        .menu-card h3 {
            font-family:'Playfair Display',serif; font-size:1.2rem; margin-bottom:8px;
        }
        .menu-card p { font-size:0.9rem; color:#777; flex:1; line-height:1.6; }
        .menu-card .price {
            font-size:1.1rem; color:var(--gold);
            font-weight:700; margin-top:14px;
        }
        .hidden { display:none; }

        footer {
            background:var(--dark); color:#aaa;
            text-align:center; padding:24px;
        }
    </style>
</head>
<body>
<nav>
    <div class="logo">The Golden Fork</div>
    <a href="index.php">← Back to Home</a>
</nav>

<div class="page-hero">
    <h1>Our Menu</h1>
    <p>Fresh · Local · Crafted with Love</p>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="filterMenu('all')">All</button>
    <button class="tab-btn" onclick="filterMenu('starters')">Starters</button>
    <button class="tab-btn" onclick="filterMenu('mains')">Mains</button>
    <button class="tab-btn" onclick="filterMenu('desserts')">Desserts</button>
    <button class="tab-btn" onclick="filterMenu('drinks')">Drinks</button>
</div>

<div class="menu-grid" id="menuGrid">
<?php
$result = $conn->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name");
while ($item = $result->fetch_assoc()):
?>
    <div class="menu-card" data-category="<?= $item['category'] ?>">
        <div class="tag"><?= $item['category'] ?></div>
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p><?= htmlspecialchars($item['description']) ?></p>
        <div class="price">LKR <?= number_format($item['price'], 2) ?></div>
    </div>
<?php endwhile; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> The Golden Fork Restaurant</p>
</footer>

<script>
function filterMenu(cat) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');

    document.querySelectorAll('.menu-card').forEach(card => {
        if (cat === 'all' || card.dataset.category === cat) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}
</script>
</body>
</html>