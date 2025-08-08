<?php
// teamleader_dashboard.php
session_start();
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teamleader') {
    header("Location: index.php"); exit();
}

// Teamleader can chat with Staff and IT
$stmt = $conn->prepare("SELECT id, fullname, username, role FROM users WHERE role IN ('staff','it') ORDER BY role, fullname");
$stmt->execute();
$users = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Team Leader Dashboard</title>
<style>body{font-family:Arial;padding:20px} table{border-collapse:collapse;width:80%} td,th{border:1px solid #ccc;padding:8px}</style>
</head>
<body>
<h2>Team Leader Dashboard</h2>
<p>Welcome! <a href="logout.php">Logout</a></p>

<h3>Registered Staff / IT</h3>
<table>
<tr><th>Name</th><th>Username</th><th>Role</th><th>Action</th></tr>
<?php while($u = $users->fetch_assoc()): ?>
<tr>
    <td><?=htmlspecialchars($u['fullname'])?></td>
    <td><?=htmlspecialchars($u['username'])?></td>
    <td><?=htmlspecialchars($u['role'])?></td>
    <td><a href="chat.php?user_id=<?=$u['id']?>">Chat</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
