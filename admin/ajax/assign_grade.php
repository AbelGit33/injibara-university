<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_course_id = $_POST['student_course_id'] ?? 0;
    $grade = $_POST['grade'] ?? '';
    
    if (!$student_course_id || !$grade) {
        echo json_encode(['success' => false]);
        exit();
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE student_courses SET grade=? WHERE id=?");
    $stmt->bind_param("si", $grade, $student_course_id);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
    $conn->close();
}
?>
