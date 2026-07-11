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
        $student_id = $_POST['student_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];
        $program = $_POST['program'];
        $year_of_study = $_POST['year_of_study'];
        $status = $_POST['status'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, email, phone, gender, program, year_of_study, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssis", $student_id, $first_name, $last_name, $email, $phone, $gender, $program, $year_of_study, $status);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE students SET student_id=?, first_name=?, last_name=?, email=?, phone=?, gender=?, program=?, year_of_study=?, status=? WHERE id=?");
            $stmt->bind_param("sssssssisi", $student_id, $first_name, $last_name, $email, $phone, $gender, $program, $year_of_study, $status, $id);
        }
        
        $stmt->execute();
        $message = "Student " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "Student deleted successfully!";
    }
}

// Get students
$students = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Students</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-user-graduate"></i> Students Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add Student
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Search and Filter -->
            <div class="filter-bar">
                <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'studentsTable')" placeholder="Search students...">
                <select onchange="filterStatus(this.value)">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Graduated">Graduated</option>
                </select>
            </div>
            
            <!-- Students Table -->
            <div class="table-card">
                <table class="data-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$student['id']; ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['program']); ?></td>
                            <td><?php echo htmlspecialchars((string)$student['year_of_study'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars(strtolower($student['status']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($student['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <button class="btn-icon" onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo (int)$student['id']; ?>, 'student')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn-icon" onclick="viewStudent(<?php echo (int)$student['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <!-- Add/Edit Modal -->
    <div class="modal" id="studentModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Student</h2>
            <form method="POST" action="" id="studentForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="studentId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="student_id" id="student_id" required>
                    </div>
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" id="phone">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" id="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Program *</label>
                        <input type="text" name="program" id="program" required>
                    </div>
                    <div class="form-group">
                        <label>Year of Study</label>
                        <select name="year_of_study" id="year_of_study">
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Graduated">Graduated</option>
                        <option value="Suspended">Suspended</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Student
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('studentModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add Student';
                document.getElementById('formAction').value = 'add';
                document.getElementById('studentForm').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit Student';
                document.getElementById('formAction').value = 'edit';
                // Fill form with data
                document.getElementById('studentId').value = data.id;
                document.getElementById('student_id').value = data.student_id;
                document.getElementById('first_name').value = data.first_name;
                document.getElementById('last_name').value = data.last_name;
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('gender').value = data.gender;
                document.getElementById('program').value = data.program;
                document.getElementById('year_of_study').value = data.year_of_study;
                document.getElementById('status').value = data.status;
            }
        }
        
        function editStudent(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('studentModal').classList.remove('active');
        }
        
        function filterStatus(status) {
            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const statusCell = rows[i].getElementsByTagName('td')[6];
                if (status === '' || statusCell.textContent.trim() === status) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
