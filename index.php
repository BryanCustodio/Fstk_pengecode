<?php
// index.php
session_start();
require "db.php";

if (isset($_SESSION['user_id'])) {
    // already logged in -> redirect based on role
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
            // redirect to role dashboard
            if ($row['role'] == 'staff') header("Location: staff_dashboard.php");
            if ($row['role'] == 'teamleader') header("Location: teamleader_dashboard.php");
            if ($row['role'] == 'it') header("Location: it_dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - Helpdesk</title>
    <style>body{font-family:Arial;padding:30px} input{padding:8px;margin:6px}</style>
</head>
<body>
<h2>Login</h2>
<form method="POST">
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
<p>Wala pang account? <a href="register.php">Register dito</a></p>
<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
</body>
</html>
