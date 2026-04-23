<?php

// Auto-detect environment
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {

    // ── LOCAL (XAMPP) ──────────────────────
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "restaurant_db";

} else {

    // ── PRODUCTION (InfinityFree) ──────────
    $host = "sql301.infinityfree.com";
    $user = "if0_41732893";
    $pass = "Gimsara2004";
    $db   = "if0_41732893_dineease";

}

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>