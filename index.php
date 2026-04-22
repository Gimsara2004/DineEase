<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Golden Fork Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #c9a84c;
            --dark: #1a1a1a;
            --cream: #f9f5ef;
            --text: #333;
        }

        body {
            font-family: 'Lato', sans-serif;
            color: var(--text);
            background: var(--cream);
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0; width: 100%;
            background: rgba(26,26,26,0.95);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-size: 1.6rem;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }
        nav ul a {
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        nav ul a:hover { color: var(--gold); }

        /* HERO */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                        url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1600') center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            margin-bottom: 16px;
        }
        .hero p {
            font-size: 1.2rem;
            font-weight: 300;
            margin-bottom: 36px;
            letter-spacing: 2px;
        }
        .btn {
            display: inline-block;
            padding: 14px 36px;
            background: var(--gold);
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        .btn:hover { background: #a8863c; }

        /* SECTIONS */
        section { padding: 80px 40px; }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 10px;
        }
        .gold-line {
            width: 60px;
            height: 3px;
            background: var(--gold);
            margin: 0 auto 50px;
        }

        /* ABOUT */
        .about {
            background: #fff;
            display: flex;
            gap: 60px;
            align-items: center;
            max-width: 1100px;
            margin: 0 auto;
        }
        .about img {
            width: 45%;
            border-radius: 4px;
        }
        .about-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 16px;
        }
        .about-text p {
            line-height: 1.8;
            color: #555;
            margin-bottom: 12px;
        }

        /* MENU PREVIEW */
        .menu-section { background: var(--cream); }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .menu-card {
            background: #fff;
            padding: 28px;
            border-bottom: 3px solid var(--gold);
        }
        .menu-card .category-tag {
            font-size: 0.75rem;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .menu-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        .menu-card p {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 12px;
        }
        .menu-card .price {
            font-size: 1.1rem;
            color: var(--gold);
            font-weight: 700;
        }
        .center { text-align: center; margin-top: 40px; }

        /* RESERVATION */
        .reservation-section { background: var(--dark); color: #fff; }
        .reservation-section .section-title { color: #fff; }
        form {
            max-width: 700px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        form .full-width { grid-column: 1 / -1; }
        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-family: 'Lato', sans-serif;
            font-size: 0.95rem;
            border-radius: 2px;
            outline: none;
            transition: border 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--gold);
        }
        select option { color: #333; background: #fff; }
        textarea { resize: vertical; min-height: 100px; }
        .submit-btn {
            grid-column: 1 / -1;
            padding: 16px;
            background: var(--gold);
            color: #fff;
            border: none;
            font-size: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover { background: #a8863c; }
        .alert {
            grid-column: 1 / -1;
            padding: 14px;
            border-radius: 2px;
            text-align: center;
            display: none;
        }
        .alert.success { background: #2d6a4f; color: #fff; display: block; }
        .alert.error   { background: #922b21; color: #fff; display: block; }

        /* CONTACT */
        .contact-section { background: #fff; }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .contact-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }
        .contact-info p {
            margin-bottom: 12px;
            color: #555;
            line-height: 1.7;
        }
        .contact-info span {
            color: var(--gold);
            font-weight: 700;
        }
        .contact-form input,
        .contact-form textarea {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            margin-bottom: 16px;
        }
        .contact-form input:focus,
        .contact-form textarea:focus { border-color: var(--gold); }
        .contact-btn {
            padding: 14px 36px;
            background: var(--gold);
            color: #fff;
            border: none;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s;
        }
        .contact-btn:hover { background: #a8863c; }

        /* FOOTER */
        footer {
            background: var(--dark);
            color: #aaa;
            text-align: center;
            padding: 30px;
            font-size: 0.9rem;
        }
        footer span { color: var(--gold); }
    </style>
</head>
<body>

<!-- NAVIGATION -->
<nav>
    <div class="logo">The Golden Fork</div>
    <ul>
        <li><a href="#about">About</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#reservation">Reserve</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<!-- HERO -->
<div class="hero">
    <h1>The Golden Fork</h1>
    <p>Fine Dining · Fresh Ingredients · Unforgettable Experience</p>
    <a href="#reservation" class="btn">Book a Table</a>
</div>

<!-- ABOUT -->
<section id="about" style="background:#fff; padding:80px 40px;">
    <h2 class="section-title">Our Story</h2>
    <div class="gold-line"></div>
    <div class="about">
        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?w=600" alt="Restaurant interior">
        <div class="about-text">
            <h2>A Tradition of Excellence</h2>
            <p>Established in the heart of the city, The Golden Fork has been serving exceptional cuisine since 2010. We believe that great food is not just about flavour — it is about the experience, the atmosphere, and the memories you take home.</p>
            <p>Our chefs use only the freshest locally sourced ingredients, crafting dishes that honour traditional recipes while embracing modern culinary techniques.</p>
            <p>Whether you are joining us for a romantic dinner, a family celebration, or a business lunch — we promise an experience worth returning to.</p>
        </div>
    </div>
</section>

<!-- MENU PREVIEW -->
<section id="menu" class="menu-section">
    <h2 class="section-title">Our Menu</h2>
    <div class="gold-line"></div>
    <div class="menu-grid">
        <?php
        $result = $conn->query("SELECT * FROM menu_items WHERE is_available = 1 LIMIT 6");
        while ($item = $result->fetch_assoc()):
        ?>
        <div class="menu-card">
            <div class="category-tag"><?= htmlspecialchars($item['category']) ?></div>
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <div class="price">LKR <?= number_format($item['price'], 2) ?></div>
        </div>
        <?php endwhile; ?>
    </div>
    <div class="center">
        <a href="menu.php" class="btn">View Full Menu</a>
    </div>
</section>

<!-- RESERVATION -->
<section id="reservation" class="reservation-section">
    <h2 class="section-title">Reserve a Table</h2>
    <div class="gold-line"></div>
    <?php
    $msg = '';
    $msgClass = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
        $name    = $conn->real_escape_string($_POST['name']);
        $phone   = $conn->real_escape_string($_POST['phone']);
        $email   = $conn->real_escape_string($_POST['email']);
        $date    = $conn->real_escape_string($_POST['date']);
        $time    = $conn->real_escape_string($_POST['time']);
        $guests  = (int)$_POST['guests'];
        $message = $conn->real_escape_string($_POST['message']);

        $sql = "INSERT INTO reservations (name, phone, email, date, time, guests, message)
                VALUES ('$name','$phone','$email','$date','$time',$guests,'$message')";

        if ($conn->query($sql)) {
            $msg = "✓ Thank you $name! Your reservation is confirmed. We will contact you shortly.";
            $msgClass = 'success';
        } else {
            $msg = "✗ Something went wrong. Please call us directly.";
            $msgClass = 'error';
        }
    }
    ?>
    <form method="POST">
        <?php if ($msg): ?>
            <div class="alert <?= $msgClass ?>"><?= $msg ?></div>
        <?php endif; ?>
        <input type="text"   name="name"    placeholder="Your Full Name"    required>
        <input type="tel"    name="phone"   placeholder="Phone Number"       required>
        <input type="email"  name="email"   placeholder="Email Address"      required>
        <input type="date"   name="date"    min="<?= date('Y-m-d') ?>"       required>
        <select name="time" required>
            <option value="">Select Time</option>
            <option>12:00 PM</option>
            <option>12:30 PM</option>
            <option>01:00 PM</option>
            <option>07:00 PM</option>
            <option>07:30 PM</option>
            <option>08:00 PM</option>
            <option>08:30 PM</option>
            <option>09:00 PM</option>
        </select>
        <select name="guests" required>
            <option value="">Number of Guests</option>
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
            <?php endfor; ?>
        </select>
        <textarea name="message" class="full-width" placeholder="Special requests or dietary requirements..."></textarea>
        <button type="submit" name="reserve" class="submit-btn">Confirm Reservation</button>
    </form>
</section>

<!-- CONTACT -->
<section id="contact" class="contact-section">
    <h2 class="section-title">Contact Us</h2>
    <div class="gold-line"></div>
    <div class="contact-grid">
        <div class="contact-info">
            <h3>Find Us</h3>
            <p>📍 <span>Address:</span> 42 Main Street, Negombo, Sri Lanka</p>
            <p>📞 <span>Phone:</span> +94 31 222 3456</p>
            <p>✉️ <span>Email:</span> info@goldenfork.lk</p>
            <p>🕐 <span>Lunch:</span> 11:30 AM – 3:00 PM</p>
            <p>🕐 <span>Dinner:</span> 6:30 PM – 10:30 PM</p>
            <p>📅 <span>Open:</span> Tuesday – Sunday (Closed Mondays)</p>
        </div>
        <div class="contact-form">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact'])) {
                $cname    = $conn->real_escape_string($_POST['cname']);
                $cemail   = $conn->real_escape_string($_POST['cemail']);
                $cphone   = $conn->real_escape_string($_POST['cphone']);
                $cmessage = $conn->real_escape_string($_POST['cmessage']);
                $conn->query("INSERT INTO messages (name, email, phone, message)
                              VALUES ('$cname','$cemail','$cphone','$cmessage')");
                echo "<p style='color:green; margin-bottom:16px;'>✓ Message sent! We'll get back to you soon.</p>";
            }
            ?>
            <form method="POST">
                <input type="text"  name="cname"    placeholder="Your Name"    required>
                <input type="email" name="cemail"   placeholder="Your Email"   required>
                <input type="tel"   name="cphone"   placeholder="Phone Number">
                <textarea name="cmessage" rows="4"  placeholder="Your Message" required></textarea>
                <button type="submit" name="contact" class="contact-btn">Send Message</button>
            </form>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p>&copy; <?= date('Y') ?> <span>The Golden Fork Restaurant</span>. All rights reserved.</p>
</footer>

</body>
</html>