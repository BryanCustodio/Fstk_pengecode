<?php
// send_message.php
session_start();
require "db.php";
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit(); }

$from = (int)$_SESSION['user_id'];
$to = isset($_POST['to']) ? (int)$_POST['to'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($to <= 0 || $message === '') { http_response_code(400); exit(); }

// optional: validate that both users exist
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $from, $to, $message);
$stmt->execute();
echo "ok";
