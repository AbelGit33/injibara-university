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
        $college_code = $_POST['college_code'];
        $college_name = $_POST['college_name'];
        $dean = $_POST['dean'];
        $description = $_POST['description'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO colleges (college_code, college_name, dean, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $college_code, $college_name, $dean, $description);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE colleges SET college_code=?, college_name=?, dean=?, description=? WHERE id=?");
            $stmt->bind_param("ssssi", $college_code, $college_name, $dean, $description, $id);
        }
        
        $stmt->execute();
        $message = "College " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM colleges WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "College deleted successfully!";
    }
}

$colleges = $conn->query("SELECT * FROM colleges ORDER BY college_name");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Colleges</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-university"></i> Colleges Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add College
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <table class="data-table" id="collegesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>College Name</th>
                            <th>Dean</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($college = $colleges->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$college['id']; ?></td>
                            <td><?php echo htmlspecialchars($college['college_code']); ?></td>
                            <td><?php echo htmlspecialchars($college['college_name']); ?></td>
                            <td><?php echo htmlspecialchars($college['dean']); ?></td>
                            <td><?php echo substr(htmlspecialchars($college['description']), 0, 50) . '...'; ?></td>
                            <td>
                                <button class="btn-icon" onclick="editCollege(<?php echo htmlspecialchars(json_encode($college)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo (int)$college['id']; ?>, 'college')">
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
    <div class="modal" id="collegeModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add College</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="collegeId">
                
                <div class="form-group">
                    <label>College Code *</label>
                    <input type="text" name="college_code" id="college_code" required>
                </div>
                
                <div class="form-group">
                    <label>College Name *</label>
                    <input type="text" name="college_name" id="college_name" required>
                </div>
                
                <div class="form-group">
                    <label>Dean</label>
                    <input type="text" name="dean" id="dean">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="4"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save College
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('collegeModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add College';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#collegeModal form').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit College';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('collegeId').value = data.id;
                document.getElementById('college_code').value = data.college_code;
                document.getElementById('college_name').value = data.college_name;
                document.getElementById('dean').value = data.dean || '';
                document.getElementById('description').value = data.description || '';
            }
        }
        
        function editCollege(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('collegeModal').classList.remove('active');
        }
    </script>
</body>
</html>
