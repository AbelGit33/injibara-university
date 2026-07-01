<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once('../config/database.php');
$conn = getConnection();

// Get statistics
$stats = [
    'students' => $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'],
    'faculty' => $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'],
    'courses' => $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'],
    'colleges' => $conn->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'],
    'announcements' => $conn->query("SELECT COUNT(*) as count FROM announcements WHERE status='Published'")->fetch_assoc()['count'],
    'attendance_today' => $conn->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE()")->fetch_assoc()['count']
];

// Get recent activities
$recent_students = $conn->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
$recent_announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INU Admin - Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../images/inu-logo.png" alt="INU" onerror="this.src='https://www.inu.edu.et/wp-content/uploads/2024/06/INU_DARK_LOGO1.png'">
            <h2>INU ADMIN</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="students.php" class="nav-item">
                <i class="fas fa-user-graduate"></i> Students
            </a>
            <a href="faculty.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i> Faculty
            </a>
            <a href="colleges.php" class="nav-item">
                <i class="fas fa-university"></i> Colleges
            </a>
            <a href="courses.php" class="nav-item">
                <i class="fas fa-book"></i> Courses
            </a>
            <a href="attendance.php" class="nav-item">
                <i class="fas fa-calendar-check"></i> Attendance
            </a>
            <a href="grades.php" class="nav-item">
                <i class="fas fa-chart-line"></i> Grades
            </a>
            <a href="announcements.php" class="nav-item">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            <a href="news.php" class="nav-item">
                <i class="fas fa-newspaper"></i> News
            </a>
            <a href="reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="top-bar-right">
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <i class="fas fa-user-circle"></i>
                    <div class="dropdown-menu">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <h1 class="page-title">Dashboard</h1>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['students']); ?></h3>
                        <p>Students</p>
                    </div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['faculty']); ?></h3>
                        <p>Faculty</p>
                    </div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['courses']); ?></h3>
                        <p>Courses</p>
                    </div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['colleges']); ?></h3>
                        <p>Colleges</p>
                    </div>
                </div>
                
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['announcements']); ?></h3>
                        <p>Announcements</p>
                    </div>
                </div>
                
                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['attendance_today']); ?></h3>
                        <p>Attendance Today</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <div class="chart-card">
                    <h3>Student Enrollment by College</h3>
                    <canvas id="enrollmentChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Attendance Overview</h3>
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity Tables -->
            <div class="tables-row">
                <div class="table-card">
                    <h3><i class="fas fa-user-graduate"></i> Recent Students</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($student = $recent_students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['program']); ?></td>
                                <td><span class="badge <?php echo strtolower($student['status']); ?>"><?php echo $student['status']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-card">
                    <h3><i class="fas fa-bullhorn"></i> Recent Announcements</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($announcement = $recent_announcements->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                <td><?php echo $announcement['category']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($announcement['publish_date'])); ?></td>
                                <td><span class="badge <?php echo strtolower($announcement['status']); ?>"><?php echo $announcement['status']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="js/admin.js"></script>
    <script>
        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(enrollmentCtx, {
            type: 'bar',
            data: {
                labels: ['College of Agriculture', 'College of Business', 'College of Education', 'College of Engineering', 'College of Medicine', 'College of Science'],
                datasets: [{
                    label: 'Students',
                    data: [1200, 1500, 1100, 1800, 900, 1400],
                    backgroundColor: '#1a5f23'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(attendanceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [85, 10, 5],
                    backgroundColor: ['#2e7d32', '#d32f2f', '#f4a261']
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
