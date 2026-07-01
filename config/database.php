<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'injibara_university');

// Create database connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

// Initialize database and tables
function initializeDatabase() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->select_db(DB_NAME);
    
    // Create tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            full_name VARCHAR(100),
            role ENUM('super_admin', 'admin', 'staff') DEFAULT 'admin',
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS students (
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE,
            phone VARCHAR(20),
            gender ENUM('Male', 'Female', 'Other'),
            date_of_birth DATE,
            address TEXT,
            college_id INT,
            department_id INT,
            program VARCHAR(100),
            year_of_study INT,
            semester INT,
            enrollment_date DATE,
            status ENUM('Active', 'Inactive', 'Graduated', 'Suspended') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS faculty (
            id INT PRIMARY KEY AUTO_INCREMENT,
            faculty_id VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE,
            phone VARCHAR(20),
            gender ENUM('Male', 'Female', 'Other'),
            department VARCHAR(100),
            qualification VARCHAR(100),
            specialization TEXT,
            hire_date DATE,
            status ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS colleges (
            id INT PRIMARY KEY AUTO_INCREMENT,
            college_code VARCHAR(10) UNIQUE NOT NULL,
            college_name VARCHAR(100) NOT NULL,
            dean VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS departments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            department_code VARCHAR(10) UNIQUE NOT NULL,
            department_name VARCHAR(100) NOT NULL,
            college_id INT,
            head_of_department VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS courses (
            id INT PRIMARY KEY AUTO_INCREMENT,
            course_code VARCHAR(20) UNIQUE NOT NULL,
            course_name VARCHAR(100) NOT NULL,
            department_id INT,
            credit_hours INT,
            semester INT,
            year INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS student_courses (
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id INT,
            course_id INT,
            semester INT,
            year INT,
            grade VARCHAR(5),
            status ENUM('Enrolled', 'Completed', 'Failed', 'Withdrawn') DEFAULT 'Enrolled',
            enrollment_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS attendance (
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id INT,
            course_id INT,
            attendance_date DATE,
            status ENUM('Present', 'Absent', 'Late') DEFAULT 'Present',
            marked_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS announcements (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            content TEXT,
            category ENUM('Academic', 'Administrative', 'Event', 'Emergency') DEFAULT 'Academic',
            posted_by INT,
            publish_date DATE,
            status ENUM('Draft', 'Published', 'Archived') DEFAULT 'Draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS news (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            content TEXT,
            image VARCHAR(255),
            author_id INT,
            publish_date DATE,
            status ENUM('Draft', 'Published', 'Archived') DEFAULT 'Draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        $conn->query($sql);
    }
    
    // Create default admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT IGNORE INTO admin_users (username, password, email, full_name, role) 
                  VALUES ('admin', '$hashed_password', 'admin@inu.edu.et', 'System Administrator', 'super_admin')");
    
    $conn->close();
}

// Call initialization
initializeDatabase();
?>
