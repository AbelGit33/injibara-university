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

// Get current settings (you can store these in a settings table)
$settings = [
    'site_name' => 'Injibara University',
    'site_email' => 'injibarauniversity@inu.edu.et',
    'site_phone' => '+251-(0)58-227-21-11',
    'site_address' => '40 PO Box, Injibara, Ethiopia',
    'academic_year' => '2025/2026',
    'current_semester' => '1st Semester'
];

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // In a real app, you'd save these to database
    $message = "Settings updated successfully!";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INU Admin - Settings</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="main-content">
        <?php include('includes/topbar.php'); ?>
        
        <div class="content-wrapper">
            <h1 class="page-title"><i class="fas fa-cog"></i> System Settings</h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="settings-grid">
                <!-- General Settings -->
                <div class="card">
                    <h3><i class="fas fa-university"></i> General Settings</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="update_settings" value="1">
                        
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Site Email</label>
                            <input type="email" name="site_email" value="<?php echo $settings['site_email']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="site_phone" value="<?php echo $settings['site_phone']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="site_address" rows="3"><?php echo $settings['site_address']; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
                
                <!-- Academic Settings -->
                <div class="card">
                    <h3><i class="fas fa-graduation-cap"></i> Academic Settings</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Current Academic Year</label>
                            <input type="text" name="academic_year" value="<?php echo $settings['academic_year']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Current Semester</label>
                            <select name="current_semester">
                                <option value="1st Semester" <?php echo $settings['current_semester']=='1st Semester'?'selected':''; ?>>1st Semester</option>
                                <option value="2nd Semester" <?php echo $settings['current_semester']=='2nd Semester'?'selected':''; ?>>2nd Semester</option>
                                <option value="Summer" <?php echo $settings['current_semester']=='Summer'?'selected':''; ?>>Summer</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Enable Registration</label>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Enable Grade Submission</label>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </form>
                </div>
                
                <!-- System Info -->
                <div class="card">
                    <h3><i class="fas fa-info-circle"></i> System Information</h3>
                    <table class="info-table">
                        <tr>
                            <td>PHP Version</td>
                            <td><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <td>Server Software</td>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>Database</td>
                            <td>MySQL <?php echo $conn->server_info ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td>System Time</td>
                            <td><?php echo date('Y-m-d H:i:s'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
