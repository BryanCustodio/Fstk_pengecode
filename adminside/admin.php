<?php
// register.php
session_start();
require "../db.php";

$err = "";
$ok = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_number = trim($_POST['employee_number']);
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($employee_number=="" || $fullname=="" || $username=="" || $password=="" ) {
        $err = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $err = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (employee_number, fullname, username, password, role) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("sssss", $employee_number, $fullname, $username, $hash, $role);
            if ($ins->execute()) {
                $ok = "Registration successful. You may now login.";
            } else {
                $err = "Error saving user.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register - Helpdesk</title>
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
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 0;
        }
        .register-card {
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 380px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .register-card h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .register-card input,
        .register-card select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }
        .register-card button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .register-card button:hover {
            background: #45a049;
        }
        .message {
            font-size: 14px;
            margin-top: 10px;
        }
        .error { color: red; }
        .success { color: green; }
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
<div class="register-card">
    <h2>Create Account</h2>
    <form method="POST">
        <input name="employee_number" placeholder="Employee Number" required>
        <input name="fullname" placeholder="Full Name" required>
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <select name="role">
            <option value="teamleader">Team Leader</option>
            <option value="it">IT</option>
        </select>
        <button type="submit">Register</button>
    </form>
    <p class="message">Already have an account? <a href="../index.php">Login here</a></p>
    <?php if ($err) echo "<p class='message error'>$err</p>"; ?>
    <?php if ($ok) echo "<p class='message success'>$ok</p>"; ?>
</div>
</body>
</html>
