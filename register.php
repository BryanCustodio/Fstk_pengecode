<?php
// register.php
session_start();
require "db.php";

$err = "";
$ok = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_number = trim($_POST['employee_number']);
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // 'staff' | 'teamleader' | 'it'

    if ($employee_number=="" || $fullname=="" || $username=="" || $password=="" ) {
        $err = "Please fill all fields.";
    } else {
        // check username unique
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
<head><meta charset="utf-8"><title>Register</title>
<style>body{font-family:Arial;padding:20px} input,select{padding:8px;margin:6px}</style>
</head>
<body>
<h2>Register</h2>
<form method="POST">
    <input name="employee_number" placeholder="Employee Number" required><br>
    <input name="fullname" placeholder="Full Name" required><br>
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <label>Role:
        <select name="role">
            <option value="staff">Staff</option>
            <option value="teamleader">Team Leader</option>
            <option value="it">IT</option>
        </select>
    </label>
    <br>
    <button type="submit">Register</button>
</form>
<p><a href="index.php">Back to login</a></p>
<?php if ($err) echo "<p style='color:red'>$err</p>"; ?>
<?php if ($ok) echo "<p style='color:green'>$ok</p>"; ?>
</body>
</html>
