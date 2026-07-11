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

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $course_code = $_POST['course_code'];
        $course_name = $_POST['course_name'];
        $credit_hours = $_POST['credit_hours'];
        $semester = $_POST['semester'];
        $year = $_POST['year'];
        $description = $_POST['description'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, credit_hours, semester, year, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiss", $course_code, $course_name, $credit_hours, $semester, $year, $description);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_name=?, credit_hours=?, semester=?, year=?, description=? WHERE id=?");
            $stmt->bind_param("ssiissi", $course_code, $course_name, $credit_hours, $semester, $year, $description, $id);
        }
        
        $stmt->execute();
        $message = "Course " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "Course deleted successfully!";
    }
}

$courses = $conn->query("SELECT c.*, d.department_name FROM courses c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.course_code");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Courses</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-book"></i> Courses Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add Course
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <table class="data-table" id="coursesTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Semester</th>
                            <th>Year</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars((string)$course['credit_hours'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['semester'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string)$course['year'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['department_name'] ?? 'N/A'); ?></td>
                            <td>
                                <button class="btn-icon" onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo (int)$course['id']; ?>, 'course')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <!-- Modal -->
    <div class="modal" id="courseModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Course</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="courseId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Course Code *</label>
                        <input type="text" name="course_code" id="course_code" required>
                    </div>
                    <div class="form-group">
                        <label>Course Name *</label>
                        <input type="text" name="course_name" id="course_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Credit Hours *</label>
                        <input type="number" name="credit_hours" id="credit_hours" min="1" max="6" required>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester" id="semester">
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                            <option value="3">Summer</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Year</label>
                    <select name="year" id="year">
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Course
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('courseModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add Course';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#courseModal form').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit Course';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('courseId').value = data.id;
                document.getElementById('course_code').value = data.course_code;
                document.getElementById('course_name').value = data.course_name;
                document.getElementById('credit_hours').value = data.credit_hours;
                document.getElementById('semester').value = data.semester;
                document.getElementById('year').value = data.year;
                document.getElementById('description').value = data.description || '';
            }
        }
        
        function editCourse(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('courseModal').classList.remove('active');
        }
    </script>
</body>
</html>
