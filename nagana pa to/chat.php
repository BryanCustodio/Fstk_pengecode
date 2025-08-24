<?php
session_start();
require "db.php";
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$me = (int)$_SESSION['user_id'];
$other = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($other <= 0) { echo "No user selected."; exit(); }

$stmt = $conn->prepare("SELECT id, fullname, username, role, employee_number FROM users WHERE id = ?");
$stmt->bind_param("i",$other);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) { echo "User not found."; exit(); }
$otherUser = $res->fetch_assoc();

$update = $conn->prepare("UPDATE messages SET status = 'read', is_read = 1 
                          WHERE sender_id = ? AND receiver_id = ? AND status = 'unread'");
$update->bind_param("ii", $other, $me);
$update->execute();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Chat with <?=htmlspecialchars($otherUser['fullname'])?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    .chat-container {
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 80vh;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .chat-header {
        background: #8e44ad;
        color: white;
        padding: 15px;
        font-size: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-header a {
        color: white;
        text-decoration: none;
        font-size: 14px;
    }
    #chat-box {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
    }
    .msg {
        padding: 8px 12px;
        margin: 4px 0;
        border-radius: 18px;
        max-width: 75%;
        word-wrap: break-word;
    }
    .me {
        background: #d1ffd1;
        align-self: flex-end;
    }
    .them {
        background: #d1e0ff;
        align-self: flex-start;
    }
    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 8px;
        background: #fff;
    }
    .chat-input input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
    }
    .chat-input button {
        background: #8e44ad;
        color: white;
        border: none;
        padding: 10px 16px;
        margin-left: 8px;
        border-radius: 20px;
        cursor: pointer;
    }
    .chat-input button:hover {
        background: #732d91;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <span>💬 <?=htmlspecialchars($otherUser['fullname'])?> (<?=htmlspecialchars($otherUser['role'])?>)</span>
        <a href="<?php
            if ($_SESSION['role']=='staff') echo 'staff_dashboard.php';
            elseif ($_SESSION['role']=='teamleader') echo 'teamleader_dashboard.php';
            else echo 'it_dashboard.php';
        ?>">← Back</a>
    </div>

    <div id="chat-box"></div>

    <form id="chat-form" class="chat-input">
        <input type="text" id="message" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>
</div>

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
