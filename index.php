<?php
// index.php
session_start();
require "db.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'staff') header("Location: staff_dashboard.php");
    if ($_SESSION['role'] == 'teamleader') header("Location: teamleader_dashboard.php");
    if ($_SESSION['role'] == 'it') header("Location: it_dashboard.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['role'] = $row['role'];
            if ($row['role'] == 'staff') header("Location: staff_dashboard.php");
            if ($row['role'] == 'teamleader') header("Location: teamleader_dashboard.php");
            if ($row['role'] == 'it') header("Location: it_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - Helpdesk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('bg.jpg') no-repeat center center/cover;
        }
        /* dark overlay para malinaw yung form */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 0;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .login-card h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-card input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }
        .login-card button {
            width: 100%;
            padding: 10px;
            background: #3f51b5;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-card button:hover {
            background: #2c3e9a;
        }
        .login-card p {
            margin-top: 15px;
            font-size: 14px;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        a {
            color: #3f51b5;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-card">
    <h2>Helpdesk Login</h2>
    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Wala pang account?<a href="register.php">Register dito</a></p>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>
