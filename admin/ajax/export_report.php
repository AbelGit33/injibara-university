<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../../config/database.php');

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

$type = $_GET['type'] ?? '';

$conn = getConnection();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

switch($type) {
    case 'students':
        fputcsv($output, ['ID', 'Student ID', 'Name', 'Email', 'Program', 'Year', 'Status']);
        $result = $conn->query("SELECT id, student_id, CONCAT(first_name, ' ', last_name) as name, email, program, year_of_study, status FROM students");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;
        
    case 'attendance':
        fputcsv($output, ['Date', 'Student', 'Course', 'Status']);
        $result = $conn->query("
            SELECT a.attendance_date, CONCAT(s.first_name, ' ', s.last_name) as student, c.course_name, a.status 
            FROM attendance a 
            JOIN students s ON a.student_id = s.id 
            JOIN courses c ON a.course_id = c.id 
            WHERE MONTH(a.attendance_date) = MONTH(CURDATE())
        ");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;
        
    case 'grades':
        fputcsv($output, ['Student', 'Course', 'Grade', 'Status']);
        $result = $conn->query("
            SELECT CONCAT(s.first_name, ' ', s.last_name) as student, c.course_name, sc.grade, sc.status 
            FROM student_courses sc 
            JOIN students s ON sc.student_id = s.id 
            JOIN courses c ON sc.course_id = c.id
        ");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;
}

fclose($output);
$conn->close();
?>
