<?php
session_start();
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM admin_users WHERE username='$username'");
    $user   = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin']    = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Wrong username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — DineEase</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background: linear-gradient(135deg, #1a1208, #2d1f0a);
      min-height:100vh;
      display:flex; align-items:center; justify-content:center;
    }
    .login-box {
      background:#fff;
      border-radius:12px;
      padding:48px 44px;
      width:100%; max-width:420px;
      box-shadow:0 20px 60px rgba(0,0,0,0.4);
    }
    .logo {
      text-align:center; margin-bottom:32px;
    }
    .logo h1 {
      font-size:30px; color:#c8913a;
      font-style:italic; font-weight:700;
    }
    .logo p { font-size:13px; color:#aaa; margin-top:4px; }

    .form-group { margin-bottom:18px; }
    .form-group label {
      display:block; font-size:12px;
      font-weight:700; color:#555;
      letter-spacing:0.5px; margin-bottom:6px;
    }
    .form-group input {
      width:100%; padding:12px 16px;
      border:1px solid #ddd; border-radius:8px;
      font-size:14px; outline:none;
      transition:border-color 0.2s;
      font-family:'Segoe UI',sans-serif;
    }
    .form-group input:focus { border-color:#c8913a; }

    .error-msg {
      background:#f8d7da; color:#721c24;
      padding:10px 14px; border-radius:6px;
      font-size:13px; margin-bottom:18px;
      border:1px solid #f5c6cb;
    }
    .btn-login {
      width:100%; background:#c8913a; color:#fff;
      border:none; padding:14px;
      font-size:15px; font-weight:700;
      border-radius:8px; cursor:pointer;
      transition:background 0.2s;
    }
    .btn-login:hover { background:#a87030; }
    .footer-note {
      text-align:center; margin-top:24px;
      font-size:12px; color:#bbb;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="logo">
      <h1>DineEase</h1>
      <p>Admin Panel — Secure Login</p>
    </div>

    <?php if ($error): ?>
      <div class="error-msg">❌ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required autofocus/>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required/>
      </div>
      <button type="submit" class="btn-login">Login to Admin Panel</button>
    </form>

    <div class="footer-note">
      DineEase Restaurant Management System
    </div>
  </div>
</body>
</html>