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
    $student_id = (int)$student_id;
    $course_id = (int)$course_id;

    $allowed_statuses = ['Present', 'Absent', 'Late'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'Present';
    }

    $check = $conn->prepare("SELECT id FROM attendance WHERE student_id=? AND course_id=? AND attendance_date=CURDATE()");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $update = $conn->prepare("UPDATE attendance SET status=? WHERE student_id=? AND course_id=? AND attendance_date=CURDATE()");
        $update->bind_param("sii", $status, $student_id, $course_id);
        $update->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, course_id, status, marked_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $student_id, $course_id, $status, $marked_by);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true]);
    $conn->close();
}
?>
