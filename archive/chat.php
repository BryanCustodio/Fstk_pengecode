<?php
// chat.php
session_start();
require "db.php";
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$me = (int)$_SESSION['user_id'];
$other = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($other <= 0) { echo "No user selected."; exit(); }

// check the other user exists
$stmt = $conn->prepare("SELECT id, fullname, username, role, employee_number FROM users WHERE id = ?");
$stmt->bind_param("i",$other);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) { echo "User not found."; exit(); }
$otherUser = $res->fetch_assoc();

// optional: you may add access rules here (e.g., staff cannot chat staff) - already enforced via dashboards
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Chat with <?=htmlspecialchars($otherUser['fullname'])?></title>
<style>
body{font-family:Arial;padding:20px}
#chat-box{border:1px solid #ccc;height:400px;overflow:auto;padding:10px;background:#f9f9f9;}
.msg{padding:6px;margin:6px;border-radius:6px;display:inline-block;max-width:80%}
.me{background:#d1ffd1;align-self:flex-end}
.them{background:#d1e0ff}
.container{display:flex;flex-direction:column}
form{margin-top:10px}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<a href="<?php
    // back link depending on role
    if ($_SESSION['role']=='staff') echo 'staff_dashboard.php';
    elseif ($_SESSION['role']=='teamleader') echo 'teamleader_dashboard.php';
    else echo 'it_dashboard.php';
?>">&larr; Back</a>
<h3>Chat with <?=htmlspecialchars($otherUser['fullname'])?> (<?=htmlspecialchars($otherUser['role'])?>)</h3>
<p><strong>Employee No:</strong> <?=htmlspecialchars($otherUser['employee_number'] ?? '')?></p>

<div id="chat-box" class="container"></div>

<form id="chat-form">
    <input type="text" id="message" placeholder="Type message..." style="width:70%;padding:8px" required>
    <button type="submit">Send</button>
</form>

<script>
var me = <?=json_encode($me)?>;
var other = <?=json_encode($other)?>;

function loadMessages(){
    $.get("load_messages.php", { user_id: other }, function(data){
        $("#chat-box").html(data);
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

$("#chat-form").submit(function(e){
    e.preventDefault();
    var msg = $("#message").val().trim();
    if (!msg) return;
    $.post("send_message.php", { to: other, message: msg }, function(){
        $("#message").val("");
        loadMessages();
    });
});

setInterval(loadMessages, 2000);
loadMessages();
</script>
</body>
</html>
