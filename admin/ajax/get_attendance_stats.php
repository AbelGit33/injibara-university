<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once('../../config/database.php');

$conn = getConnection();

// Get today's attendance stats
$present = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date=CURDATE() AND status='Present'")->fetch_assoc()['count'];
$absent = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date=CURDATE() AND status='Absent'")->fetch_assoc()['count'];
$late = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date=CURDATE() AND status='Late'")->fetch_assoc()['count'];

echo json_encode([
    'present' => $present,
    'absent' => $absent,
    'late' => $late
]);

$conn->close();
?>
