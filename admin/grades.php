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

// Get student courses with grades
$student_courses = $conn->query("
    SELECT sc.id, s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
           c.course_code, c.course_name, sc.grade, sc.status
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.id
    JOIN courses c ON sc.course_id = c.id
    ORDER BY s.student_id, c.course_code
");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Grades</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <h1 class="page-title"><i class="fas fa-chart-line"></i> Grades Management</h1>
            
            <div class="table-card">
                <table class="data-table" id="gradesTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Course Name</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($sc = $student_courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sc['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($sc['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($sc['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($sc['course_name']); ?></td>
                            <td>
                                <select class="grade-select" onchange="assignGrade(<?php echo (int)$sc['id']; ?>, this.value)">
                                    <option value="">Select</option>
                                    <option value="A" <?php echo $sc['grade']=='A'?'selected':''; ?>>A</option>
                                    <option value="B+" <?php echo $sc['grade']=='B+'?'selected':''; ?>>B+</option>
                                    <option value="B" <?php echo $sc['grade']=='B'?'selected':''; ?>>B</option>
                                    <option value="C+" <?php echo $sc['grade']=='C+'?'selected':''; ?>>C+</option>
                                    <option value="C" <?php echo $sc['grade']=='C'?'selected':''; ?>>C</option>
                                    <option value="D" <?php echo $sc['grade']=='D'?'selected':''; ?>>D</option>
                                    <option value="F" <?php echo $sc['grade']=='F'?'selected':''; ?>>F</option>
                                </select>
                            </td>
                            <td><span class="badge <?php echo htmlspecialchars(strtolower($sc['status']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($sc['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <button class="btn-icon" onclick="updateStatus(<?php echo (int)$sc['id']; ?>, '<?php echo $sc['status']==='Completed'?'Enrolled':'Completed'; ?>')">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <script src="js/admin.js"></script>
    <script>
        function assignGrade(studentCourseId, grade) {
            if (!grade) return;
            
            fetch('ajax/assign_grade.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'student_course_id=' + studentCourseId + '&grade=' + grade
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Grade assigned successfully!');
                } else {
                    alert('Error assigning grade');
                }
            });
        }
        
        function updateStatus(id, newStatus) {
            fetch('ajax/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'type=student_course&id=' + id + '&status=' + newStatus
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
