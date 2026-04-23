<?php
include 'config/db.php';

// Load menu items from database (featured ones for homepage)
$featured_items = $conn->query("SELECT * FROM menu_items WHERE is_featured=1 AND is_available=1 LIMIT 6");

// Load all available menu items grouped by category
$starters  = $conn->query("SELECT * FROM menu_items WHERE category='starters'  AND is_available=1");
$mains     = $conn->query("SELECT * FROM menu_items WHERE category='mains'     AND is_available=1");
$desserts  = $conn->query("SELECT * FROM menu_items WHERE category='desserts'  AND is_available=1");
$drinks    = $conn->query("SELECT * FROM menu_items WHERE category='drinks'    AND is_available=1");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DineEase — Fine Dining Restaurant</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary: #c8913a;
      --dark:    #1a1208;
      --light:   #fdf6ec;
      --text:    #3a2e1e;
      --accent:  #8b0000;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Lato',sans-serif; background:var(--light); color:var(--text); overflow-x:hidden; }
    html { scroll-behavior:smooth; }

    /* ── NAV ── */
    nav {
      position:fixed; top:0; width:100%; z-index:1000;
      padding:0 60px; display:flex;
      justify-content:space-between; align-items:center;
      height:70px;
      background:linear-gradient(to bottom, rgba(26,18,8,0.95), transparent);
      transition:background 0.3s;
    }
    nav.scrolled { background:rgba(26,18,8,0.98); box-shadow:0 2px 20px rgba(0,0,0,0.3); }
    .nav-logo { font-family:'Playfair Display',serif; font-size:22px; color:var(--primary); font-style:italic; text-decoration:none; }
    .nav-links { display:flex; gap:32px; list-style:none; align-items:center; }
    .nav-links a { color:#e8d5b0; text-decoration:none; font-size:12px; letter-spacing:2px; text-transform:uppercase; transition:color 0.2s; }
    .nav-links a:hover { color:var(--primary); }
    .nav-reserve {
      background:var(--primary); color:#fff !important;
      padding:8px 20px !important; letter-spacing:1px !important;
      transition:background 0.2s !important;
    }
    .nav-reserve:hover { background:var(--accent) !important; }
    .nav-toggle { display:none; background:none; border:none; color:#fff; font-size:24px; cursor:pointer; }

    /* ── HERO ── */
    #hero {
      min-height:100vh;
      background:linear-gradient(135deg, #1a1208 0%, #2d1f0a 50%, #1a1208 100%);
      display:flex; align-items:center; justify-content:center;
      text-align:center; position:relative; overflow:hidden;
    }
    #hero::before {
      content:'';
      position:absolute; inset:0;
      background:radial-gradient(ellipse at center, rgba(200,145,58,0.12) 0%, transparent 70%);
    }
    .hero-pattern {
      position:absolute; inset:0;
      background-image:
        repeating-linear-gradient(45deg,  transparent, transparent 40px, rgba(200,145,58,0.03) 40px, rgba(200,145,58,0.03) 41px),
        repeating-linear-gradient(-45deg, transparent, transparent 40px, rgba(200,145,58,0.03) 40px, rgba(200,145,58,0.03) 41px);
    }
    .hero-content { position:relative; z-index:1; padding:40px 20px; }
    .hero-tag {
      display:inline-block; font-size:11px; letter-spacing:4px;
      text-transform:uppercase; color:var(--primary);
      border:1px solid rgba(200,145,58,0.4); padding:6px 20px;
      margin-bottom:28px; animation:fadeUp 0.8s ease both;
    }
    .hero-title {
      font-family:'Playfair Display',serif;
      font-size:clamp(48px,8vw,90px);
      color:#f5e6cc; line-height:1.05; margin-bottom:20px;
      animation:fadeUp 0.8s 0.15s ease both;
    }
    .hero-title em { color:var(--primary); font-style:italic; }
    .hero-desc {
      font-size:16px; color:#b09070; max-width:520px;
      margin:0 auto 36px; line-height:1.7; font-weight:300;
      animation:fadeUp 0.8s 0.3s ease both;
    }
    .hero-btns { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; animation:fadeUp 0.8s 0.45s ease both; }
    .btn-primary {
      background:var(--primary); color:#fff; padding:14px 36px;
      border:none; font-family:'Lato',sans-serif; font-size:12px;
      font-weight:700; letter-spacing:2px; text-transform:uppercase;
      cursor:pointer; text-decoration:none; transition:all 0.2s; display:inline-block;
    }
    .btn-primary:hover { background:var(--accent); transform:translateY(-2px); }
    .btn-outline {
      background:transparent; color:#e8d5b0; padding:14px 36px;
      border:1px solid rgba(232,213,176,0.4); font-family:'Lato',sans-serif;
      font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
      cursor:pointer; text-decoration:none; transition:all 0.2s; display:inline-block;
    }
    .btn-outline:hover { border-color:var(--primary); color:var(--primary); transform:translateY(-2px); }
    .scroll-hint {
      position:absolute; bottom:30px; left:50%; transform:translateX(-50%);
      display:flex; flex-direction:column; align-items:center; gap:8px;
    }
    .scroll-hint span { font-size:10px; letter-spacing:3px; color:#8a7060; text-transform:uppercase; }
    .scroll-line { width:1px; height:40px; background:linear-gradient(to bottom, var(--primary), transparent); animation:scrollPulse 1.5s infinite; }

    /* ── SECTION COMMONS ── */
    .section-tag { font-size:11px; letter-spacing:3px; text-transform:uppercase; color:var(--primary); margin-bottom:14px; display:block; }
    .section-heading { font-family:'Playfair Display',serif; font-size:clamp(30px,4vw,46px); line-height:1.15; margin-bottom:20px; }
    .section-heading em { color:var(--primary); font-style:italic; }
    .section-text { font-size:15px; color:#5a4e3e; line-height:1.8; font-weight:300; margin-bottom:16px; }

    /* ── ABOUT ── */
    #about { padding:100px 60px; display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:center; max-width:1200px; margin:0 auto; }
    .about-img { position:relative; }
    .about-img-box {
      width:100%; padding-bottom:110%;
      background:linear-gradient(135deg, #2d1f0a, #4a3520);
      position:relative; overflow:hidden;
    }
    .about-img-box::before { content:'🍽️'; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:80px; opacity:0.25; }
    .about-badge {
      position:absolute; bottom:-20px; right:-20px;
      background:var(--primary); color:#fff;
      width:100px; height:100px; border-radius:50%;
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      font-family:'Playfair Display',serif; text-align:center;
    }
    .about-badge .yr { font-size:22px; font-weight:700; }
    .about-badge .yr-lbl { font-size:10px; letter-spacing:1px; }
    .stats-row { display:flex; gap:32px; margin-top:32px; }
    .stat { text-align:center; }
    .stat-num { font-family:'Playfair Display',serif; font-size:36px; color:var(--primary); font-weight:700; }
    .stat-lbl { font-size:11px; letter-spacing:2px; text-transform:uppercase; color:#8a7060; }

    /* ── MENU ── */
    #menu { background:var(--dark); padding:100px 60px; }
    .menu-header { text-align:center; margin-bottom:48px; }
    .menu-header .section-heading { color:#f5e6cc; }
    .menu-tabs { display:flex; justify-content:center; gap:4px; margin-bottom:40px; flex-wrap:wrap; }
    .tab-btn {
      background:transparent; color:#8a7060;
      border:1px solid #3a2e1e; padding:8px 24px;
      font-family:'Lato',sans-serif; font-size:11px;
      letter-spacing:2px; text-transform:uppercase;
      cursor:pointer; transition:all 0.2s;
    }
    .tab-btn.active, .tab-btn:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
    .menu-grid {
      display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr));
      gap:2px; max-width:1100px; margin:0 auto;
    }
    .menu-card {
      background:#221a0a; padding:28px;
      border:1px solid #2e2010;
      transition:border-color 0.2s, transform 0.2s;
    }
    .menu-card:hover { border-color:var(--primary); transform:translateY(-3px); }
    .menu-card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
    .menu-card-name { font-family:'Playfair Display',serif; font-size:18px; color:#f5e6cc; }
    .menu-card-price { font-size:16px; color:var(--primary); font-weight:700; font-family:'Playfair Display',serif; }
    .menu-card-desc { font-size:13px; color:#7a6a54; line-height:1.6; }
    .menu-card-img { width:100%; height:160px; object-fit:cover; margin-bottom:14px; border-radius:2px; }
    .menu-card-img-placeholder { width:100%; height:160px; background:linear-gradient(135deg,#2d1f0a,#4a3520); display:flex; align-items:center; justify-content:center; font-size:40px; margin-bottom:14px; }
    .menu-tag { display:inline-block; margin-top:10px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:var(--primary); border:1px solid rgba(200,145,58,0.3); padding:3px 8px; }
    .no-items { text-align:center; color:#5a4a32; padding:48px; font-size:15px; grid-column:1/-1; }

    /* ── SPECIALS ── */
    #specials { padding:100px 60px; max-width:1200px; margin:0 auto; text-align:center; }
    .specials-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:32px; margin-top:48px; }
    .special-card {
      background:#fff; border:1px solid #e8d8c0; padding:40px 28px;
      position:relative; transition:transform 0.2s, box-shadow 0.2s;
    }
    .special-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(200,145,58,0.1); }
    .special-icon { font-size:40px; margin-bottom:16px; }
    .special-card h3 { font-family:'Playfair Display',serif; font-size:22px; color:var(--dark); margin-bottom:12px; }
    .special-card p { font-size:14px; color:#7a6a54; line-height:1.7; }
    .special-ribbon { position:absolute; top:16px; right:16px; background:var(--accent); color:#fff; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; padding:4px 10px; }

    /* ── GALLERY ── */
    #gallery { background:var(--dark); padding:100px 60px; }
    .gallery-grid { display:grid; grid-template-columns:repeat(4,1fr); grid-template-rows:repeat(2,200px); gap:4px; max-width:1100px; margin:48px auto 0; }
    .gallery-cell { background:linear-gradient(135deg,#2d1f0a,#4a3520); position:relative; overflow:hidden; cursor:pointer; }
    .gallery-cell:first-child { grid-column:span 2; grid-row:span 2; }
    .gallery-cell::before { content:attr(data-emoji); position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:48px; opacity:0.3; }
    .gallery-cell::after { content:''; position:absolute; inset:0; background:rgba(200,145,58,0); transition:background 0.3s; }
    .gallery-cell:hover::after { background:rgba(200,145,58,0.2); }

    /* ── RESERVATION ── */
    #reservation { padding:100px 60px; display:grid; grid-template-columns:1fr 1fr; gap:80px; max-width:1200px; margin:0 auto; }
    .res-info h2 { font-family:'Playfair Display',serif; font-size:42px; color:var(--dark); margin-bottom:24px; }
    .contact-detail { display:flex; gap:16px; align-items:flex-start; margin-bottom:20px; }
    .contact-icon { width:44px; height:44px; min-width:44px; background:rgba(200,145,58,0.1); border:1px solid rgba(200,145,58,0.3); display:flex; align-items:center; justify-content:center; font-size:18px; }
    .contact-detail-text strong { display:block; font-size:12px; letter-spacing:2px; text-transform:uppercase; color:var(--primary); margin-bottom:4px; }
    .contact-detail-text span { font-size:14px; color:#5a4e3e; line-height:1.6; }
    .res-form-box { background:var(--dark); padding:48px; }
    .res-form-box h3 { font-family:'Playfair Display',serif; font-size:28px; color:#f5e6cc; margin-bottom:28px; }
    .form-group { margin-bottom:16px; }
    .form-group input, .form-group select, .form-group textarea {
      width:100%; background:#2e2010; border:1px solid #3a2e1e;
      color:#e8d5b0; padding:12px 16px; font-family:'Lato',sans-serif;
      font-size:14px; outline:none; transition:border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--primary); }
    .form-group select option { background:#2e2010; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .form-msg { padding:12px 16px; border-radius:4px; font-size:14px; margin-bottom:16px; display:none; }
    .form-msg.success { background:rgba(39,174,96,0.2); color:#27ae60; border:1px solid rgba(39,174,96,0.3); display:block; }
    .form-msg.error   { background:rgba(231,76,60,0.2);  color:#e74c3c; border:1px solid rgba(231,76,60,0.3);  display:block; }

    /* ── FOOTER ── */
    footer { background:#0e0b04; padding:48px 60px 32px; text-align:center; }
    .footer-logo { font-family:'Playfair Display',serif; font-size:32px; color:var(--primary); font-style:italic; margin-bottom:16px; }
    .footer-links { display:flex; justify-content:center; gap:28px; list-style:none; margin-bottom:28px; flex-wrap:wrap; }
    .footer-links a { color:#6a5a44; font-size:11px; letter-spacing:2px; text-transform:uppercase; text-decoration:none; transition:color 0.2s; }
    .footer-links a:hover { color:var(--primary); }
    .footer-divider { border:none; border-top:1px solid #2e2010; margin:0 0 24px; }
    .footer-copy { font-size:12px; color:#4a3a28; }

    /* ── TOAST ── */
    #toast {
      position:fixed; bottom:30px; right:30px; z-index:9999;
      padding:14px 24px; border-radius:8px; font-size:14px;
      font-weight:600; display:none; box-shadow:0 8px 24px rgba(0,0,0,0.2);
    }
    #toast.success { background:#27ae60; color:#fff; }
    #toast.error   { background:#e74c3c; color:#fff; }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
    @keyframes scrollPulse { 0%,100% { opacity:0.4; } 50% { opacity:1; } }

    /* ── RESPONSIVE ── */
    @media(max-width:900px) {
      nav { padding:0 20px; }
      .nav-links { display:none; flex-direction:column; position:absolute; top:70px; left:0; width:100%; background:#1a1208; padding:20px; }
      .nav-links.open { display:flex; }
      .nav-toggle { display:block; }
      #about, #reservation { grid-template-columns:1fr; gap:40px; padding:60px 20px; }
      #menu, #specials, #gallery { padding:60px 20px; }
      .specials-grid { grid-template-columns:1fr; }
      .gallery-grid { grid-template-columns:1fr 1fr; }
      .gallery-cell:first-child { grid-column:span 2; grid-row:span 1; }
      .stats-row { gap:16px; }
      .form-row { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>

<!-- ── NAV ── -->
<nav id="main-nav">
  <a href="#" class="nav-logo">DineEase</a>
  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
  <ul class="nav-links">
    <li><a href="#about">About</a></li>
    <li><a href="#menu">Menu</a></li>
    <li><a href="#specials">Experience</a></li>
    <li><a href="#gallery">Gallery</a></li>
    <li><a href="#reservation" class="nav-reserve">Reserve</a></li>
  </ul>
</nav>

<!-- ── HERO ── -->
<section id="hero">
  <div class="hero-pattern"></div>
  <div class="hero-content">
    <div class="hero-tag">Fine Dining · Sri Lankan · Seafood</div>
    <h1 class="hero-title">
      DineEase<br/>
      <em>Authentic Flavours,<br/>Timeless Moments</em>
    </h1>
    <p class="hero-desc">A culinary journey through the finest ingredients, prepared with passion and served with love in the heart of Negombo.</p>
    <div class="hero-btns">
      <a href="#menu"        class="btn-primary">View Our Menu</a>
      <a href="#reservation" class="btn-outline">Reserve a Table</a>
    </div>
  </div>
  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>
</section>

<!-- ── ABOUT ── -->
<section id="about">
  <div class="about-img">
    <div class="about-img-box"></div>
    <div class="about-badge">
      <span class="yr">Est.</span>
      <span class="yr-lbl">2005</span>
    </div>
  </div>
  <div>
    <span class="section-tag">Our Story</span>
    <h2 class="section-heading">Where Every Meal<br/>Becomes a <em>Memory</em></h2>
    <p class="section-text">We believe that great food is more than just nourishment — it is an experience that brings people together, creates memories, and tells a story of our land, our culture, and our people.</p>
    <p class="section-text">From the freshest catch at Negombo's famous fish market to the spices of our herb garden, every ingredient is chosen with care and every dish prepared with love.</p>
    <div class="stats-row">
      <div class="stat"><div class="stat-num">500+</div><div class="stat-lbl">Happy Guests Daily</div></div>
      <div class="stat"><div class="stat-num">80+</div><div class="stat-lbl">Dishes on Menu</div></div>
      <div class="stat"><div class="stat-num">20+</div><div class="stat-lbl">Years of Excellence</div></div>
    </div>
  </div>
</section>

<!-- ── MENU ── -->
<section id="menu">
  <div class="menu-header">
    <span class="section-tag">Our Selection</span>
    <h2 class="section-heading">Crafted With <em>Passion</em></h2>
  </div>

  <div class="menu-tabs">
    <button class="tab-btn active" onclick="showTab('starters',this)">Starters</button>
    <button class="tab-btn"        onclick="showTab('mains',this)">Main Course</button>
    <button class="tab-btn"        onclick="showTab('desserts',this)">Desserts</button>
    <button class="tab-btn"        onclick="showTab('drinks',this)">Drinks</button>
  </div>

  <!-- STARTERS -->
  <div class="menu-grid" id="tab-starters">
    <?php if ($starters->num_rows === 0): ?>
      <div class="no-items">🍽️ No starters available right now.</div>
    <?php else: ?>
      <?php while ($item = $starters->fetch_assoc()): ?>
        <div class="menu-card">
          <?php if ($item['image']): ?>
            <img src="admin/uploads/menu/<?= htmlspecialchars($item['image']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php elseif ($item['image_url']): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php else: ?>
            <div class="menu-card-img-placeholder">🥗</div>
          <?php endif; ?>
          <div class="menu-card-top">
            <span class="menu-card-name"><?= htmlspecialchars($item['name']) ?></span>
            <span class="menu-card-price">Rs. <?= number_format($item['price'],0) ?></span>
          </div>
          <p class="menu-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          <?php if ($item['is_featured']): ?><span class="menu-tag">Chef's Pick</span><?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <!-- MAINS -->
  <div class="menu-grid" id="tab-mains" style="display:none">
    <?php if ($mains->num_rows === 0): ?>
      <div class="no-items">🍽️ No main course items available right now.</div>
    <?php else: ?>
      <?php while ($item = $mains->fetch_assoc()): ?>
        <div class="menu-card">
          <?php if ($item['image']): ?>
            <img src="admin/uploads/menu/<?= htmlspecialchars($item['image']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php elseif ($item['image_url']): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php else: ?>
            <div class="menu-card-img-placeholder">🍛</div>
          <?php endif; ?>
          <div class="menu-card-top">
            <span class="menu-card-name"><?= htmlspecialchars($item['name']) ?></span>
            <span class="menu-card-price">Rs. <?= number_format($item['price'],0) ?></span>
          </div>
          <p class="menu-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          <?php if ($item['is_featured']): ?><span class="menu-tag">Chef's Pick</span><?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <!-- DESSERTS -->
  <div class="menu-grid" id="tab-desserts" style="display:none">
    <?php if ($desserts->num_rows === 0): ?>
      <div class="no-items">🍽️ No desserts available right now.</div>
    <?php else: ?>
      <?php while ($item = $desserts->fetch_assoc()): ?>
        <div class="menu-card">
          <?php if ($item['image']): ?>
            <img src="admin/uploads/menu/<?= htmlspecialchars($item['image']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php elseif ($item['image_url']): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php else: ?>
            <div class="menu-card-img-placeholder">🍮</div>
          <?php endif; ?>
          <div class="menu-card-top">
            <span class="menu-card-name"><?= htmlspecialchars($item['name']) ?></span>
            <span class="menu-card-price">Rs. <?= number_format($item['price'],0) ?></span>
          </div>
          <p class="menu-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          <?php if ($item['is_featured']): ?><span class="menu-tag">Chef's Pick</span><?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <!-- DRINKS -->
  <div class="menu-grid" id="tab-drinks" style="display:none">
    <?php if ($drinks->num_rows === 0): ?>
      <div class="no-items">🍽️ No drinks available right now.</div>
    <?php else: ?>
      <?php while ($item = $drinks->fetch_assoc()): ?>
        <div class="menu-card">
          <?php if ($item['image']): ?>
            <img src="admin/uploads/menu/<?= htmlspecialchars($item['image']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php elseif ($item['image_url']): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="menu-card-img" alt="<?= htmlspecialchars($item['name']) ?>"/>
          <?php else: ?>
            <div class="menu-card-img-placeholder">🥤</div>
          <?php endif; ?>
          <div class="menu-card-top">
            <span class="menu-card-name"><?= htmlspecialchars($item['name']) ?></span>
            <span class="menu-card-price">Rs. <?= number_format($item['price'],0) ?></span>
          </div>
          <p class="menu-card-desc"><?= htmlspecialchars($item['description']) ?></p>
          <?php if ($item['is_featured']): ?><span class="menu-tag">Chef's Pick</span><?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<!-- ── SPECIALS ── -->
<section id="specials">
  <span class="section-tag">Why Choose Us</span>
  <h2 class="section-heading">The <em>DineEase</em> Experience</h2>
  <div class="specials-grid">
    <div class="special-card">
      <div class="special-icon">🦞</div>
      <h3>Fresh Catch Daily</h3>
      <p>Our seafood arrives fresh from Negombo's famous fishing harbour every morning, guaranteeing the finest quality on your plate.</p>
      <span class="special-ribbon">Daily</span>
    </div>
    <div class="special-card">
      <div class="special-icon">👨‍🍳</div>
      <h3>Master Chefs</h3>
      <p>Our culinary team brings over 20 years of combined experience in Sri Lankan, Asian, and Continental cuisine.</p>
    </div>
    <div class="special-card">
      <div class="special-icon">🌿</div>
      <h3>Garden to Table</h3>
      <p>Herbs, spices, and vegetables sourced from our own organic garden and trusted local farmers every single day.</p>
      <span class="special-ribbon">Organic</span>
    </div>
  </div>
</section>

<!-- ── GALLERY ── -->
<section id="gallery">
  <div style="text-align:center">
    <span class="section-tag">Visual Story</span>
    <h2 class="section-heading" style="color:#f5e6cc">A Feast for the <em>Eyes</em></h2>
  </div>
  <div class="gallery-grid">
    <div class="gallery-cell" data-emoji="🦞"></div>
    <div class="gallery-cell" data-emoji="🍛"></div>
    <div class="gallery-cell" data-emoji="🦐"></div>
    <div class="gallery-cell" data-emoji="🍷"></div>
    <div class="gallery-cell" data-emoji="🥗"></div>
  </div>
</section>

<!-- ── RESERVATION ── -->
<section id="reservation">
  <div class="res-info">
    <span class="section-tag">Book a Table</span>
    <h2>Visit Us &amp; Reserve<br/>Your <em>Table</em></h2>
    <br/>
    <div class="contact-detail">
      <div class="contact-icon">📍</div>
      <div class="contact-detail-text">
        <strong>Address</strong>
        <span>123 Lewis Place, Negombo, Sri Lanka</span>
      </div>
    </div>
    <div class="contact-detail">
      <div class="contact-icon">📞</div>
      <div class="contact-detail-text">
        <strong>Phone</strong>
        <span>+94 31 222 3456</span>
      </div>
    </div>
    <div class="contact-detail">
      <div class="contact-icon">✉️</div>
      <div class="contact-detail-text">
        <strong>Email</strong>
        <span>info@dineease.lk</span>
      </div>
    </div>
    <div class="contact-detail">
      <div class="contact-icon">🕐</div>
      <div class="contact-detail-text">
        <strong>Opening Hours</strong>
        <span>Daily 11:00 AM – 11:00 PM</span>
      </div>
    </div>
  </div>

  <div class="res-form-box">
    <h3>Reserve Your Table</h3>
    <div id="res-msg" class="form-msg"></div>
    <form id="res-form">
      <div class="form-row">
        <div class="form-group">
          <input type="text" name="name" placeholder="Your Full Name *" required/>
        </div>
        <div class="form-group">
          <input type="text" name="phone" placeholder="Phone Number *" required/>
        </div>
      </div>
      <div class="form-group">
        <input type="email" name="email" placeholder="Email Address (optional)"/>
      </div>
      <div class="form-row">
        <div class="form-group">
          <input type="date" name="date" required/>
        </div>
        <div class="form-group">
          <select name="guests" required>
            <option value="">Number of Guests</option>
            <option value="1">1 Guest</option>
            <option value="2">2 Guests</option>
            <option value="3">3 Guests</option>
            <option value="4">4 Guests</option>
            <option value="5">5 Guests</option>
            <option value="6+">6+ Guests</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <select name="time" required>
          <option value="">Select Time</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="01:00 PM">01:00 PM</option>
          <option value="02:00 PM">02:00 PM</option>
          <option value="06:00 PM">06:00 PM</option>
          <option value="07:00 PM">07:00 PM</option>
          <option value="08:00 PM">08:00 PM</option>
          <option value="09:00 PM">09:00 PM</option>
        </select>
      </div>
      <div class="form-group">
        <textarea name="message" rows="3" placeholder="Special requests or dietary requirements..."></textarea>
      </div>
      <button type="submit" class="btn-primary" style="width:100%;border:none;padding:16px;font-size:14px;" id="res-btn">
        Reserve My Table
      </button>
    </form>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="footer-logo">DineEase</div>
  <ul class="footer-links">
    <li><a href="#about">About</a></li>
    <li><a href="#menu">Menu</a></li>
    <li><a href="#specials">Experience</a></li>
    <li><a href="#gallery">Gallery</a></li>
    <li><a href="#reservation">Contact</a></li>
    <li><a href="admin/login.php">Admin</a></li>
  </ul>
  <hr class="footer-divider"/>
  <p class="footer-copy">© <?= date('Y') ?> DineEase. All rights reserved. | Negombo, Sri Lanka</p>
</footer>

<!-- ── TOAST ── -->
<div id="toast"></div>

<script>
  // Nav scroll effect
  window.addEventListener('scroll', () => {
    document.getElementById('main-nav').classList.toggle('scrolled', window.scrollY > 60);
  });

  // Set min date to today
  document.querySelector('input[name="date"]').min = new Date().toISOString().split('T')[0];

  // Menu tab switcher
  function showTab(tab, btn) {
    ['starters','mains','desserts','drinks'].forEach(t => {
      document.getElementById('tab-' + t).style.display = 'none';
    });
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).style.display = 'grid';
    btn.classList.add('active');
  }

  // Toast helper
  function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = type;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3500);
  }

  // Reservation form — AJAX submit
  document.getElementById('res-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('res-btn');
    btn.textContent = 'Sending...';
    btn.disabled = true;

    const formData = new FormData(this);

    fetch('reservation.php', { method:'POST', body: formData })
      .then(r => r.json())
      .then(data => {
        const msg = document.getElementById('res-msg');
        msg.className = 'form-msg ' + data.status;
        msg.textContent = data.message;
        msg.scrollIntoView({ behavior:'smooth', block:'center' });
        if (data.status === 'success') {
          document.getElementById('res-form').reset();
          showToast('🎉 Reservation received! We will confirm shortly.', 'success');
        }
      })
      .catch(() => {
        document.getElementById('res-msg').className = 'form-msg error';
        document.getElementById('res-msg').textContent = 'Network error. Please try again.';
      })
      .finally(() => {
        btn.textContent = 'Reserve My Table';
        btn.disabled = false;
      });
  });
</script>
</body>
</html>