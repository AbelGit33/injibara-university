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
        $title = $_POST['title'];
        $content = $_POST['content'];
        $category = $_POST['category'];
        $status = $_POST['status'];
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO announcements (title, content, category, status, posted_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $content, $category, $status, $_SESSION['admin_id']);
        } else {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE announcements SET title=?, content=?, category=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $content, $category, $status, $id);
        }
        
        $stmt->execute();
        $message = "Announcement " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "Announcement deleted successfully!";
    }
}

$announcements = $conn->query("SELECT a.*, u.full_name as author FROM announcements a LEFT JOIN admin_users u ON a.posted_by = u.id ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Announcements</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-bullhorn"></i> Announcements</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add Announcement
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <table class="data-table" id="announcementsTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($a = $announcements->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['title']); ?></td>
                            <td><?php echo htmlspecialchars($a['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['author'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($a['publish_date'])); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars(strtolower($a['status']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <button class="btn-icon" onclick="editAnnouncement(<?php echo htmlspecialchars(json_encode($a)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo (int)$a['id']; ?>, 'announcement')">
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
    <div class="modal" id="announcementModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Announcement</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="announcementId">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                
                <div class="form-group">
                    <label>Content *</label>
                    <textarea name="content" id="content" rows="6" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="category">
                            <option value="Academic">Academic</option>
                            <option value="Administrative">Administrative</option>
                            <option value="Event">Event</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status">
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Announcement
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(action, data = null) {
            document.getElementById('announcementModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add Announcement';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#announcementModal form').reset();
            } else {
                document.getElementById('modalTitle').textContent = 'Edit Announcement';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('announcementId').value = data.id;
                document.getElementById('title').value = data.title;
                document.getElementById('content').value = data.content;
                document.getElementById('category').value = data.category;
                document.getElementById('status').value = data.status;
            }
        }
        
        function editAnnouncement(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('announcementModal').classList.remove('active');
        }
    </script>
</body>
</html>
