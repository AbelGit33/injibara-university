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
        $faculty_id = $_POST['faculty_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];
        $department = $_POST['department'];
        $qualification = $_POST['qualification'];
        $specialization = $_POST['specialization'];
        $status = $_POST['status'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO faculty (faculty_id, first_name, last_name, email, phone, gender, department, qualification, specialization, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $faculty_id, $first_name, $last_name, $email, $phone, $gender, $department, $qualification, $specialization, $status);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE faculty SET faculty_id=?, first_name=?, last_name=?, email=?, phone=?, gender=?, department=?, qualification=?, specialization=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssssssi", $faculty_id, $first_name, $last_name, $email, $phone, $gender, $department, $qualification, $specialization, $status, $id);
        }
        
        $stmt->execute();
        $message = "Faculty " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'];
        $conn->query("DELETE FROM faculty WHERE id=$id");
        $message = "Faculty deleted successfully!";
    }
}

$faculty = $conn->query("SELECT * FROM faculty ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Faculty</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> Faculty Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add Faculty
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="filter-bar">
                <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'facultyTable')" placeholder="Search faculty...">
            </div>
            
            <div class="table-card">
                <table class="data-table" id="facultyTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Faculty ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Qualification</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($f = $faculty->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $f['id']; ?></td>
                            <td><?php echo htmlspecialchars($f['faculty_id']); ?></td>
                            <td><?php echo htmlspecialchars($f['first_name'] . ' ' . $f['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($f['email']); ?></td>
                            <td><?php echo htmlspecialchars($f['department']); ?></td>
                            <td><?php echo htmlspecialchars($f['qualification']); ?></td>
                            <td><span class="badge <?php echo strtolower($f['status']); ?>"><?php echo $f['status']; ?></span></td>
                            <td>
                                <button class="btn-icon" onclick="editFaculty(<?php echo htmlspecialchars(json_encode($f)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo $f['id']; ?>, 'faculty')">
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
    <div class="modal" id="facultyModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Faculty</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="facultyId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Faculty ID *</label>
                        <input type="text" name="faculty_id" id="faculty_id" required>
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
                
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" id="department">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" id="qualification" placeholder="e.g., PhD, MSc">
                    </div>
                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" name="specialization" id="specialization">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="On Leave">On Leave</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Faculty
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('facultyModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add Faculty';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#facultyModal form').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit Faculty';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('facultyId').value = data.id;
                document.getElementById('faculty_id').value = data.faculty_id;
                document.getElementById('first_name').value = data.first_name;
                document.getElementById('last_name').value = data.last_name;
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('gender').value = data.gender;
                document.getElementById('department').value = data.department || '';
                document.getElementById('qualification').value = data.qualification || '';
                document.getElementById('specialization').value = data.specialization || '';
                document.getElementById('status').value = data.status;
            }
        }
        
        function editFaculty(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('facultyModal').classList.remove('active');
        }
    </script>
</body>
</html>
