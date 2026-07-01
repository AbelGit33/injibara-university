<?php
// topbar.php - Reusable top bar component
// Note: session_start() should NOT be called here - it's called in parent files
?>
<header class="top-bar">
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="top-bar-right">
        <div class="search-box">
            <input type="text" placeholder="Search...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="user-menu">
            <span><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            <i class="fas fa-user-circle"></i>
            <div class="dropdown-menu">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>
