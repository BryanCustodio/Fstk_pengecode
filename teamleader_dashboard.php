<?php
session_start();
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teamleader') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Team Leader Dashboard</title>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f5f6fa;
    }
    header {
        background: #8e44ad;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    header h2 {
        margin: 0;
        font-size: 22px;
    }
    header a {
        color: white;
        text-decoration: none;
        background: #e74c3c;
        padding: 8px 14px;
        border-radius: 5px;
        font-size: 14px;
    }
    main {
        padding: 20px;
    }
    table.dataTable {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .badge {
        background: red;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
    }
    .btn-chat {
        background: #27ae60;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-chat:hover {
        background: #1e8449;
    }
</style>
</head>
<body>

<header>
    <h2><i class="fas fa-user-tie"></i> Team Leader Dashboard</h2>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</header>

<main>
    <h3>Registered Staff / IT</h3>
    <table id="userTable" class="display" style="width:100%">
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
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        ajax: {
            url: 'fetch_users.php?role=teamleader',
            dataSrc: function(json) {
                // Sort para mauna yung may unread messages
                json.data.sort(function(a, b) {
                    return b.unread_count - a.unread_count;
                });
                return json.data;
            }
        },
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
                    return '<a class="btn-chat" href="chat.php?user_id='+data+'"><i class="fas fa-comments"></i> Chat</a>';
                }
            }
        ],
        order: [] // Disable default ordering
    });

    // Auto-refresh every 3 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 3000);
});
</script>

</body>
</html>
