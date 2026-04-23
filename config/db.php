<?php
$host = "sql301.infinityfree.com";  // ← NOT localhost!
$user = "if0_41732893";
$pass = "Gimsara2004";     // ← your InfinityFree login password
$db   = "if0_41732893_dineease";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>