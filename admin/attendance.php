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

// Get students and courses for dropdown
$students = $conn->query("SELECT id, student_id, CONCAT(first_name, ' ', last_name) as name FROM students WHERE status='Active'");
$courses = $conn->query("SELECT id, course_code, course_name FROM courses");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Attendance</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <h1 class="page-title"><i class="fas fa-calendar-check"></i> Mark Attendance</h1>
            
            <!-- Mark Attendance Form -->
            <div class="card">
                <h3>Mark Attendance for Today (<?php echo date('M d, Y'); ?>)</h3>
                <form method="POST" action="ajax/mark_attendance.php" id="attendanceForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Student *</label>
                            <select name="student_id" required>
                                <option value="">Select Student</option>
                                <?php while($s = $students->fetch_assoc()): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['student_id'] . ' - ' . $s['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Course *</label>
                            <select name="course_id" required>
                                <option value="">Select Course</option>
                                <?php while($c = $courses->fetch_assoc()): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status *</label>
                        <div class="radio-group">
                            <label><input type="radio" name="status" value="Present" checked> Present</label>
                            <label><input type="radio" name="status" value="Absent"> Absent</label>
                            <label><input type="radio" name="status" value="Late"> Late</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Mark Attendance
                    </button>
                </form>
            </div>
            
            <!-- Today's Attendance Summary -->
            <div class="stats-grid" style="margin-top: 30px;">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-check"></i></div>
                    <div class="stat-info">
                        <h3 id="presentCount">0</h3>
                        <p>Present</p>
                    </div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon"><i class="fas fa-times"></i></div>
                    <div class="stat-info">
                        <h3 id="absentCount">0</h3>
                        <p>Absent</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3 id="lateCount">0</h3>
                        <p>Late</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="js/admin.js"></script>
    <script>
        // Load today's attendance stats
        fetch('ajax/get_attendance_stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('presentCount').textContent = data.present;
                document.getElementById('absentCount').textContent = data.absent;
                document.getElementById('lateCount').textContent = data.late;
            });
            
        // Handle form submission
        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('ajax/mark_attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Attendance marked successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>
