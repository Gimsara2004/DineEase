<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "restaurant_db";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("<p style='font-family:sans-serif;color:red;padding:30px'>
        <b>❌ DB Error:</b><br><br>" . $conn->connect_error . "
    </p>");
}
$conn->set_charset('utf8mb4');
?>