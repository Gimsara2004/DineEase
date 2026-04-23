<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dineease_db";   // ← was "restaurant_db" (WRONG)

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("<p style='font-family:sans-serif;color:red;padding:20px'>
        ❌ <b>Database connection failed.</b><br>
        Did you run setup.sql in phpMyAdmin?<br><br>
        Error: " . $conn->connect_error . "
    </p>");
}
$conn->set_charset('utf8mb4');
?>