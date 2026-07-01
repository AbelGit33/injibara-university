<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once('../config/database.php');
$conn = getConnection();

// Get statistics for reports
$stats = [
    'total_students' => $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'],
    'active_students' => $conn->query("SELECT COUNT(*) as count FROM students WHERE status='Active'")->fetch_assoc()['count'],
    'total_faculty' => $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'],
    'total_courses' => $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'],
    'attendance_today' => $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date=CURDATE()")->fetch_assoc()['count'],
    'attendance_rate' => 0
];

// Calculate attendance rate
$total_enrollments = $conn->query("SELECT COUNT(*) as count FROM student_courses WHERE status='Enrolled'")->fetch_assoc()['count'];
if ($total_enrollments > 0) {
    $stats['attendance_rate'] = round(($stats['attendance_today'] / $total_enrollments) * 100, 2);
}

// Students by college
$students_by_college = $conn->query("
    SELECT c.college_name, COUNT(s.id) as count 
    FROM students s 
    LEFT JOIN colleges c ON s.college_id = c.id 
    GROUP BY c.id 
    ORDER BY count DESC
");

// Attendance by month
$monthly_attendance = $conn->query("
    SELECT MONTH(attendance_date) as month, COUNT(*) as count 
    FROM attendance 
    WHERE YEAR(attendance_date) = YEAR(CURDATE()) 
    GROUP BY MONTH(attendance_date)
");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Reports</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <h1 class="page-title"><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
            
            <!-- Key Metrics -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_students']); ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['active_students']); ?></h3>
                        <p>Active Students</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_faculty']); ?></h3>
                        <p>Total Faculty</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-percentage"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $stats['attendance_rate']; ?>%</h3>
                        <p>Attendance Rate Today</p>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="charts-row">
                <div class="chart-card">
                    <h3>Students by College</h3>
                    <canvas id="collegeChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Monthly Attendance (<?php echo date('Y'); ?>)</h3>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            
            <!-- Export Options -->
            <div class="card" style="margin-top: 30px; padding: 25px; background: white; border-radius: 10px;">
                <h3><i class="fas fa-download"></i> Export Reports</h3>
                <div style="display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="exportReport('students')">
                        <i class="fas fa-file-excel"></i> Export Students
                    </button>
                    <button class="btn btn-success" onclick="exportReport('attendance')">
                        <i class="fas fa-file-excel"></i> Export Attendance
                    </button>
                    <button class="btn btn-info" onclick="exportReport('grades')">
                        <i class="fas fa-file-excel"></i> Export Grades
                    </button>
                    <button class="btn btn-warning" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // College Chart
        const collegeData = {
            labels: [<?php 
                $labels = [];
                while($row = $students_by_college->fetch_assoc()) {
                    $labels[] = "'" . addslashes($row['college_name'] ?? 'Unknown') . "'";
                }
                echo implode(',', $labels);
            ?>],
            datasets: [{
                label: 'Students',
                data: [<?php 
                    $students_by_college->data_seek(0);
                    $data = [];
                    while($row = $students_by_college->fetch_assoc()) {
                        $data[] = $row['count'];
                    }
                    echo implode(',', $data);
                ?>],
                backgroundColor: '#1a5f23'
            }]
        };
        
        new Chart(document.getElementById('collegeChart'), {
            type: 'bar',
            data: collegeData,
            options: { responsive: true }
        });
        
        // Monthly Attendance Chart
        const monthlyData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Attendance Records',
                data: [<?php 
                    $monthly = array_fill(0, 12, 0);
                    $monthly_attendance->data_seek(0);
                    while($row = $monthly_attendance->fetch_assoc()) {
                        $monthly[$row['month']-1] = $row['count'];
                    }
                    echo implode(',', $monthly);
                ?>],
                borderColor: '#f4a261',
                backgroundColor: 'rgba(244, 162, 97, 0.1)',
                fill: true
            }]
        };
        
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: monthlyData,
            options: { responsive: true }
        });
        
        function exportReport(type) {
            window.location.href = 'ajax/export_report.php?type=' + type;
        }
    </script>
</body>
</html>
