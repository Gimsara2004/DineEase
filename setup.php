<?php
// ============================================================
//  DINEEASE — FIRST TIME SETUP
//  Run this ONCE to create your admin account
//  Then DELETE this file immediately after!
//  Visit: http://localhost/DineEase/setup.php
// ============================================================

include 'config/db.php';

$username = "admin";
$password = "admin123";  // Change this to your own password!
$hashed   = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = $conn->query("SELECT * FROM admin_users WHERE username='$username'");
if ($check->num_rows > 0) {
    echo "<h2 style='color:orange;font-family:sans-serif;padding:30px;'>
            ⚠️ Admin user already exists!<br><br>
            <a href='admin/login.php'>Go to Login →</a>
          </h2>";
    exit();
}

$conn->query("INSERT INTO admin_users (username, password) VALUES ('$username', '$hashed')");

echo "
<!DOCTYPE html>
<html>
<head>
  <title>Setup Complete</title>
  <style>
    body { font-family:'Segoe UI',sans-serif; background:#1a1208; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .box { background:#fff; padding:48px; border-radius:12px; text-align:center; max-width:400px; }
    h2 { color:#27ae60; margin-bottom:16px; }
    p  { color:#555; margin-bottom:8px; line-height:1.6; }
    .creds { background:#f8f8f8; padding:16px; border-radius:8px; margin:20px 0; font-family:monospace; font-size:15px; }
    .btn { display:inline-block; background:#c8913a; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:700; margin-top:8px; }
    .warning { background:#fff3cd; color:#856404; padding:12px; border-radius:6px; font-size:13px; margin-top:16px; }
  </style>
</head>
<body>
  <div class='box'>
    <h2>✅ Setup Complete!</h2>
    <p>Your admin account has been created.</p>
    <div class='creds'>
      Username: <strong>$username</strong><br/>
      Password: <strong>$password</strong>
    </div>
    <a href='admin/login.php' class='btn'>Go to Admin Login →</a>
    <div class='warning'>
      ⚠️ <strong>Important:</strong> Delete this file (setup.php) immediately after logging in!
    </div>
  </div>
</body>
</html>
";
?>