<?php
// require "db.php";
// session_start();

// $role = $_GET['role'] ?? '';

// if ($role == 'it') {
//     $query = "SELECT id, employee_number, fullname, username, role 
//               FROM users 
//               WHERE role IN ('staff','teamleader') 
//               ORDER BY role, fullname";
// } elseif ($role == 'staff') {
//     $query = "SELECT id, employee_number, fullname, username, role 
//               FROM users 
//               WHERE role IN ('teamleader','it') 
//               ORDER BY role, fullname";
// } elseif ($role == 'teamleader') {
//     $query = "SELECT id, employee_number, fullname, username, role 
//               FROM users 
//               WHERE role IN ('staff','it') 
//               ORDER BY role, fullname";
// } else {
//     $query = "SELECT id, employee_number, fullname, username, role FROM users";
// }

// $result = $conn->query($query);
// $users = [];
// while ($row = $result->fetch_assoc()) {
//     $users[] = $row;
// }

// echo json_encode([
//     "data" => $users
// ]);
?>
<?php
require "db.php";
session_start();

$current_user = $_SESSION['user_id'] ?? 0;
$role = $_GET['role'] ?? '';

if ($role == 'it') {
    $query = "SELECT u.id, u.employee_number, u.fullname, u.username, u.role,
              (SELECT COUNT(*) FROM messages m 
               WHERE m.sender_id = u.id AND m.receiver_id = $current_user AND m.status = 'unread') AS unread_count
              FROM users u
              WHERE u.role IN ('staff','teamleader')
              ORDER BY unread_count DESC, u.role, u.fullname";
} elseif ($role == 'staff') {
    $query = "SELECT u.id, u.employee_number, u.fullname, u.username, u.role,
              (SELECT COUNT(*) FROM messages m 
               WHERE m.sender_id = u.id AND m.receiver_id = $current_user AND m.status = 'unread') AS unread_count
              FROM users u
              WHERE u.role IN ('teamleader','it')
              ORDER BY unread_count DESC, u.role, u.fullname";
} elseif ($role == 'teamleader') {
    $query = "SELECT u.id, u.employee_number, u.fullname, u.username, u.role,
              (SELECT COUNT(*) FROM messages m 
               WHERE m.sender_id = u.id AND m.receiver_id = $current_user AND m.status = 'unread') AS unread_count
              FROM users u
              WHERE u.role IN ('staff','it')
              ORDER BY unread_count DESC, u.role, u.fullname";
} else {
    $query = "SELECT id, employee_number, fullname, username, role, 0 AS unread_count FROM users";
}

$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["data" => $users]);
