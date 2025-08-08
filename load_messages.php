<?php
// load_messages.php
session_start();
require "db.php";
if (!isset($_SESSION['user_id'])) { echo "Not logged in"; exit(); }

$me = (int)$_SESSION['user_id'];
$other = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($other <= 0) { echo "No user specified."; exit(); }

$stmt = $conn->prepare("
    SELECT m.*, s.username AS sender_un, s.fullname AS sender_name, s.role AS sender_role
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY date_sent ASC
");
$stmt->bind_param("iiii", $me, $other, $other, $me);
$stmt->execute();
$res = $stmt->get_result();

$out = "";
while ($r = $res->fetch_assoc()) {
    $isMe = ($r['sender_id'] == $me);
    $cls = $isMe ? "me" : "them";
    $name = htmlspecialchars($r['sender_name']);
    $msg = nl2br(htmlspecialchars($r['message']));
    $time = $r['date_sent'];
    $out .= "<div class='msg $cls'><strong>{$name}:</strong><br>{$msg}<br><small>{$time}</small></div><br style='clear:both'/>";
}
echo $out;
