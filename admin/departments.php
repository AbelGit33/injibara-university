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
        $department_code = $_POST['department_code'];
        $department_name = $_POST['department_name'];
        $college_id = $_POST['college_id'];
        $head_of_department = $_POST['head_of_department'];
        $description = $_POST['description'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO departments (department_code, department_name, college_id, head_of_department, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $department_code, $department_name, $college_id, $head_of_department, $description);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE departments SET department_code=?, department_name=?, college_id=?, head_of_department=?, description=? WHERE id=?");
            $stmt->bind_param("ssiisi", $department_code, $department_name, $college_id, $head_of_department, $description, $id);
        }
        
        $stmt->execute();
        $message = "Department " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'];
        $conn->query("DELETE FROM departments WHERE id=$id");
        $message = "Department deleted successfully!";
    }
}

$departments = $conn->query("
    SELECT d.*, c.college_name 
    FROM departments d 
    LEFT JOIN colleges c ON d.college_id = c.id 
    ORDER BY d.department_name
");
$colleges = $conn->query("SELECT * FROM colleges ORDER BY college_name");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Departments</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-building"></i> Departments Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add Department
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <table class="data-table" id="departmentsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Department Name</th>
                            <th>College</th>
                            <th>Head of Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($dept = $departments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dept['department_code']); ?></td>
                            <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($dept['college_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($dept['head_of_department']); ?></td>
                            <td>
                                <button class="btn-icon" onclick="editDepartment(<?php echo htmlspecialchars(json_encode($dept)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo $dept['id']; ?>, 'department')">
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
    <div class="modal" id="departmentModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Department</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="departmentId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Department Code *</label>
                        <input type="text" name="department_code" id="department_code" required>
                    </div>
                    <div class="form-group">
                        <label>Department Name *</label>
                        <input type="text" name="department_name" id="department_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>College</label>
                    <select name="college_id" id="college_id">
                        <option value="">Select College</option>
                        <?php while($college = $colleges->fetch_assoc()): ?>
                            <option value="<?php echo $college['id']; ?>"><?php echo htmlspecialchars($college['college_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Head of Department</label>
                    <input type="text" name="head_of_department" id="head_of_department">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Department
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('departmentModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add Department';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#departmentModal form').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit Department';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('departmentId').value = data.id;
                document.getElementById('department_code').value = data.department_code;
                document.getElementById('department_name').value = data.department_name;
                document.getElementById('college_id').value = data.college_id || '';
                document.getElementById('head_of_department').value = data.head_of_department || '';
                document.getElementById('description').value = data.description || '';
            }
        }
        
        function editDepartment(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('departmentModal').classList.remove('active');
        }
    </script>
</body>
</html>
