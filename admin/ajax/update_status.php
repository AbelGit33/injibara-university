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
    
    $allowed_statuses = ['Active', 'Inactive', 'Graduated', 'Suspended', 'Enrolled', 'Completed', 'Failed', 'Withdrawn', 'Draft', 'Published', 'Archived', 'On Leave', 'Present', 'Absent', 'Late'];

    if (in_array($type, $allowed_tables) && in_array($status, $allowed_statuses)) {
        $id = (int)$id;
        $stmt = $conn->prepare("UPDATE $type SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid table']);
    }
    
    $conn->close();
}
?>
