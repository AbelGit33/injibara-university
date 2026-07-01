<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config/database.php');

// Mark attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? 0;
    $course_id = $_POST['course_id'] ?? 0;
    $status = $_POST['status'] ?? 'Present';
    
    if (!$student_id || !$course_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $conn = getConnection();
    $marked_by = $_SESSION['admin_id'] ?? 0;
    
    // Check if already marked today
    $check = $conn->query("SELECT id FROM attendance WHERE student_id=$student_id AND course_id=$course_id AND attendance_date=CURDATE()");
    
    if ($check->num_rows > 0) {
        // Update existing
        $conn->query("UPDATE attendance SET status='$status' WHERE student_id=$student_id AND course_id=$course_id AND attendance_date=CURDATE()");
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, course_id, status, marked_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $student_id, $course_id, $status, $marked_by);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true]);
    $conn->close();
}
?>
