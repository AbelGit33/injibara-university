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
        $status = $_POST['status'];
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $target_dir = "../uploads/news/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                $filename = uniqid('news_', true) . '.' . strtolower($ext);
                $image = $target_dir . $filename;
                move_uploaded_file($_FILES["image"]["tmp_name"], $image);
            }
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO news (title, content, image, author_id, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $title, $content, $image, $_SESSION['admin_id'], $status);
        } else {
            $id = $_POST['id'];
            if ($image) {
                $stmt = $conn->prepare("UPDATE news SET title=?, content=?, image=?, status=? WHERE id=?");
                $stmt->bind_param("ssssi", $title, $content, $image, $status, $id);
            } else {
                $stmt = $conn->prepare("UPDATE news SET title=?, content=?, status=? WHERE id=?");
                $stmt->bind_param("sssi", $title, $content, $status, $id);
            }
        }
        
        $stmt->execute();
        $message = "News " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM news WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "News deleted successfully!";
    }
}

$news = $conn->query("SELECT n.*, u.full_name as author FROM news n LEFT JOIN admin_users u ON n.author_id = u.id ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - News</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-newspaper"></i> News Management</h1>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add News
                </button>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <table class="data-table" id="newsTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($n = $news->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($n['title']); ?></td>
                            <td><?php echo htmlspecialchars($n['author'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($n['publish_date'])); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars(strtolower($n['status']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($n['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <?php if($n['image']): ?>
                                    <img src="<?php echo htmlspecialchars($n['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="News" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-icon" onclick="editNews(<?php echo htmlspecialchars(json_encode($n)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="confirmDelete(<?php echo (int)$n['id']; ?>, 'news')">
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
    <div class="modal" id="newsModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add News</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="newsId">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                
                <div class="form-group">
                    <label>Content *</label>
                    <textarea name="content" id="content" rows="6" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    <img id="imagePreview" style="display:none; max-width: 200px; margin-top: 10px;">
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save News
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        function openModal(action, data = null) {
            document.getElementById('newsModal').classList.add('active');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add News';
                document.getElementById('formAction').value = 'add';
                document.querySelector('#newsModal form').reset();
                document.getElementById('imagePreview').style.display = 'none';
            } else {
                document.getElementById('modalTitle').textContent = 'Edit News';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('newsId').value = data.id;
                document.getElementById('title').value = data.title;
                document.getElementById('content').value = data.content;
                document.getElementById('status').value = data.status;
                if (data.image) {
                    document.getElementById('imagePreview').src = data.image;
                    document.getElementById('imagePreview').style.display = 'block';
                }
            }
        }
        
        function editNews(data) {
            openModal('edit', data);
        }
        
        function closeModal() {
            document.getElementById('newsModal').classList.remove('active');
        }
    </script>
</body>
</html>
