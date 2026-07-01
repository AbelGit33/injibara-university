<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!$type || !$id || !$status) {
        echo json_encode(['success' => false]);
        exit();
    }
    
    $conn = getConnection();
    
    $allowed_tables = ['students', 'faculty', 'courses', 'student_courses', 'announcements', 'news'];
    
    if (in_array($type, $allowed_tables)) {
        $conn->query("UPDATE $type SET status='$status' WHERE id=$id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid table']);
    }
    
    $conn->close();
}
?>
