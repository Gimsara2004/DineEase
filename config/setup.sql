CREATE DATABASE IF NOT EXISTS if0_41732893_dineease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE if0_41732893_dineease;

CREATE TABLE IF NOT EXISTS menu_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(120)  NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2) NOT NULL,
    category     ENUM('starters','mains','desserts','drinks') NOT NULL,
    image        VARCHAR(255)  DEFAULT '',
    image_url    VARCHAR(500)  DEFAULT '',
    is_featured  TINYINT(1)    DEFAULT 0,
    is_available TINYINT(1)    DEFAULT 1,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(30)  NOT NULL,
    email      VARCHAR(120) DEFAULT '',
    date       DATE         NOT NULL,
    time       VARCHAR(20)  NOT NULL,
    guests     VARCHAR(10)  DEFAULT '2',
    message    TEXT,
    status     ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(120) DEFAULT '',
    phone      VARCHAR(30)  DEFAULT '',
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(60)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO admin_users (username, password)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO menu_items (name, description, price, category, image_url, is_featured) VALUES
('Spiced Devilled Prawns','Tiger prawns flash-fried with chilli, garlic and house devilled sauce.',950,'starters','https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=600&q=80',1),
('Crab Meat Bruschetta','Fresh crab meat on toasted sourdough with avocado and lime zest.',850,'starters','https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=600&q=80',0),
('Fish Cutlets','Traditional spiced fish cutlets with tuna and green chilli. Served with mint chutney.',700,'starters','https://images.unsplash.com/photo-1519984388953-d2406bc725e1?w=600&q=80',0),
('Calamari Rings','Crispy golden calamari with coconut batter. Served with garlic aioli.',780,'starters','https://images.unsplash.com/photo-1604909052743-94e838986d24?w=600&q=80',0),
('Soup of the Day','Freshly prepared soup from locally sourced vegetables and spices.',550,'starters','https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80',0),
('Chicken Spring Rolls','Crispy pastry filled with spiced chicken and vegetables. With sweet chilli sauce.',680,'starters','https://images.unsplash.com/photo-1544025162-d76694265947?w=600&q=80',0),
('Whole Grilled Red Snapper','Fresh red snapper grilled with lemongrass, ginger and coconut sambol. With saffron rice.',2800,'mains','https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=600&q=80',1),
('Lobster Thermidor','Half a fresh local lobster baked in cream, white wine and Gruyere sauce.',4500,'mains','https://images.unsplash.com/photo-1615361200141-f45040f367be?w=600&q=80',1),
('Butter Garlic Crab','Whole mud crab wok-tossed in butter, garlic and fresh herbs.',3200,'mains','https://images.unsplash.com/photo-1599084993091-1cb5c0721cc6?w=600&q=80',1),
('Prawn Curry','Jumbo prawns slow-cooked in Sri Lankan coconut milk curry. With string hoppers.',1800,'mains','https://images.unsplash.com/photo-1596797038530-2c107229654b?w=600&q=80',0),
('Chicken Rice and Curry','Fragrant rice with chicken curry, dhal, coconut sambol and papadums.',1400,'mains','https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=600&q=80',0),
('Grilled Beef Tenderloin','200g prime beef tenderloin with roasted vegetables, mashed potato and red wine jus.',3500,'mains','https://images.unsplash.com/photo-1558030006-450675393462?w=600&q=80',0),
('Vegetable Kottu Roti','Shredded roti wok-fried with vegetables, egg and spices.',1100,'mains','https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=600&q=80',0),
('Pasta Pescatore','Linguine with fresh prawns, calamari and mussels in tomato and white wine sauce.',2200,'mains','https://images.unsplash.com/photo-1551183053-bf91798d702e?w=600&q=80',0),
('Coconut Panna Cotta','Silky coconut panna cotta with passion fruit coulis and toasted coconut.',700,'desserts','https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&q=80',1),
('Watalappan','Classic Sri Lankan steamed jaggery custard with cashews and cardamom.',650,'desserts','https://images.unsplash.com/photo-1551024506-0bccd828d307?w=600&q=80',0),
('Chocolate Lava Cake','Warm dark chocolate fondant with vanilla ice cream and berry coulis.',850,'desserts','https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&q=80',0),
('Mango Cheesecake','Creamy baked cheesecake with fresh Alphonso mango puree.',780,'desserts','https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=600&q=80',0),
('Ice Cream 3 Scoops','Choose from vanilla, chocolate, strawberry or coconut.',550,'desserts','https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=600&q=80',0),
('Fresh King Coconut','Chilled fresh king coconut served straight from the shell.',320,'drinks','https://images.unsplash.com/photo-1550828520-4cb496926fc9?w=600&q=80',0),
('Passion Fruit Cooler','Fresh passion fruit blended with mint, lime and chilled soda.',420,'drinks','https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?w=600&q=80',0),
('Mango Lassi','Thick yoghurt blended with sweet mango and a pinch of cardamom.',480,'drinks','https://images.unsplash.com/photo-1585032226651-759b368d7246?w=600&q=80',0),
('Fresh Lime Soda','Freshly squeezed lime with still or sparkling water.',350,'drinks','https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&q=80',0),
('Ceylon Tea','Single-estate Sri Lankan black tea. Served with milk or lemon.',280,'drinks','https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=600&q=80',0),
('Watermelon Juice','Ice-cold freshly pressed watermelon juice. 100% natural.',380,'drinks','https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=600&q=80',0);