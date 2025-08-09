<!-- <?php
// session_start();
// require "db.php";
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
//     header("Location: index.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Staff Dashboard</title>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<style>
body { font-family: Arial; padding: 20px; }
</style>
</head>
<body>
<h2>Staff Dashboard</h2>
<p>Welcome! <a href="logout.php">Logout</a></p>

<h3>Available to Chat</h3>
<table id="userTable" class="display">
<thead>
<tr>
    <th>Employee Number</th>
    <th>Name</th>
    <th>Username</th>
    <th>Role</th>
    <th>Action</th>
</tr>
</thead>
</table>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        ajax: 'fetch_users.php?role=staff',
        columns: [
            { data: 'employee_number' },
            { data: 'fullname' },
            { data: 'username' },
            { data: 'role' },
            { 
                data: 'id',
                render: function(data) {
                    return '<a href="chat.php?user_id='+data+'">Chat</a>';
                }
            }
        ]
    });

    // Auto-refresh every 5 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 5000);
});
</script>
</body>
</html> -->
<?php
session_start();
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Staff Dashboard</title>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<style>
body { font-family: Arial; padding: 20px; }
.badge {
    background: red;
    color: white;
    padding: 2px 6px;
    border-radius: 50%;
    font-size: 12px;
}
</style>
</head>
<body>
<h2>Staff Dashboard</h2>
<p>Welcome! <a href="logout.php">Logout</a></p>

<h3>Registered Team Leader / IT</h3>
<table id="userTable" class="display">
<thead>
<tr>
    <th>Employee Number</th>
    <th>Name</th>
    <th>Username</th>
    <th>Role</th>
    <th>Notification</th>
    <th>Action</th>
</tr>
</thead>
</table>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        ajax: 'fetch_users.php?role=staff',
        columns: [
            { data: 'employee_number' },
            { data: 'fullname' },
            { data: 'username' },
            { data: 'role' },
            { 
                data: 'unread_count',
                render: function(data) {
                    return data > 0 ? '<span class="badge">'+data+'</span>' : '';
                }
            },
            { 
                data: 'id',
                render: function(data) {
                    return '<a href="chat.php?user_id='+data+'">Chat</a>';
                }
            }
        ]
    });

    setInterval(function() {
        table.ajax.reload(null, false);
    }, 3000);
});
</script>
</body>
</html>
