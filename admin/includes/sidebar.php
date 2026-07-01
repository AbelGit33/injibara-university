<?php
// sidebar.php - Reusable sidebar component
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../images/inu-logo.png" alt="INU" onerror="this.src='https://www.inu.edu.et/wp-content/uploads/2024/06/INU_DARK_LOGO1.png'">
        <h2>INU ADMIN</h2>
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="students.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <a href="faculty.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'faculty.php' ? 'active' : ''; ?>">
            <i class="fas fa-chalkboard-teacher"></i> Faculty
        </a>
        <a href="colleges.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'colleges.php' ? 'active' : ''; ?>">
            <i class="fas fa-university"></i> Colleges
        </a>
        <a href="departments.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>">
            <i class="fas fa-building"></i> Departments
        </a>
        <a href="courses.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i> Courses
        </a>
        <a href="attendance.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="grades.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Grades
        </a>
        <a href="announcements.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
        <a href="news.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> News
        </a>
        <a href="reports.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>
</aside>
