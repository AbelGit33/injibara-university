# Injibara University - Official Website & School Information Management System (SIMS)

A full-featured university website and school information management system for Injibara University (INU), Ethiopia. The project combines a public-facing institutional website with a comprehensive admin panel for managing students, faculty, courses, attendance, grades, and more.

## Project Overview

This project serves two main purposes:

### 1. Public Website (`index.html`)
A modern, responsive university website showcasing:
- Hero slider with campus imagery
- University statistics (students, faculty, colleges, programs)
- About / Welcome section
- Leadership profiles with modal popups
- Latest news section
- Services (SIMS, E-SHE, ICT, E-Learning portals)
- Partner organizations slider
- Contact form with validation
- Dark mode toggle
- Visitor counter
- Fully responsive design

### 2. Admin Panel (`admin/`)
A full-stack PHP/MySQL School Information Management System (SIMS) with:

| Module | Description |
|--------|-------------|
| **Dashboard** | Real-time statistics, enrollment & attendance charts (Chart.js), recent activity feeds |
| **Students** | Add, edit, delete, search, and manage student records |
| **Faculty** | Manage faculty/staff profiles, qualifications, and status |
| **Colleges** | CRUD for university colleges with dean assignment |
| **Departments** | Department management linked to colleges |
| **Courses** | Course catalog with credit hours, semester, and department association |
| **Attendance** | Mark and track daily attendance with stats via AJAX |
| **Grades** | Assign and manage student course grades |
| **Announcements** | Publish academic/administrative announcements with publish/archive workflow |
| **News** | Manage news articles with images and publish status |
| **Reports** | Generate reports, export data, attendance rate analysis |
| **Settings** | System configuration options |
| **Profile** | Admin user profile management |

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3 (Flexbox/Grid, CSS Variables, Animations), JavaScript (ES6+) |
| **Backend** | PHP 8+ (procedural with prepared statements) |
| **Database** | MySQL / MariaDB |
| **Libraries** | Chart.js (charts), Font Awesome 6 (icons), Google Fonts (Poppins) |
| **AJAX** | XMLHttpRequest for attendance marking, stats, grade assignment, export |

## Database Structure

The system uses a MySQL database (`injibara_university`) with the following tables:

- `admin_users` - System administrators (super_admin, admin, staff roles)
- `students` - Student records with enrollment info
- `faculty` - Faculty/staff profiles
- `colleges` - University colleges
- `departments` - Departments linked to colleges
- `courses` - Course catalog
- `student_courses` - Student-course enrollment with grades
- `attendance` - Daily attendance tracking
- `announcements` - Published announcements
- `news` - News articles

## Getting Started

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- A web server (Apache, Nginx, or PHP built-in server)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/injibara-university.git
cd injibara-university

# 2. Import the database
mysql -u root -p < database.sql
```
Or simply run the project in a browser — the database auto-initializes via `config/database.php`.

### 3. Configure database
Edit `config/database.php` to match your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'injibara_university');
```

### 4. Run the project
```bash
# Using PHP built-in server
php -S localhost:8000

# Or serve via Apache/Nginx pointing to the project directory
```

### 5. Access admin panel
Visit `http://localhost:8000/admin/` and login with:
- **Username:** `admin`
- **Password:** `admin123`

## Project Structure

```
injibara/
├── index.html                 # Public university website
├── README.md
├── config/
│   └── database.php           # Database connection & auto-initialization
├── css/
│   └── style.css              # Public website styles
├── js/
│   └── script.js              # Public website interactivity
├── images/                    # Local images
├── uploads/                   # User uploads
└── admin/
    ├── index.php              # Admin dashboard
    ├── login.php              # Authentication
    ├── logout.php             # Logout handler
    ├── students.php           # Student management
    ├── faculty.php            # Faculty management
    ├── colleges.php           # College management
    ├── departments.php        # Department management
    ├── courses.php            # Course management
    ├── attendance.php         # Attendance tracking
    ├── grades.php             # Grade management
    ├── announcements.php      # Announcement publishing
    ├── news.php               # News management
    ├── reports.php            # Reporting & exports
    ├── settings.php           # System settings
    ├── profile.php            # User profile
    ├── css/
    │   ├── admin.css          # Admin panel styles
    │   └── login.css          # Login page styles
    ├── js/
    │   └── admin.js           # Admin panel scripts
    ├── includes/
    │   ├── sidebar.php        # Navigation sidebar
    │   └── topbar.php         # Top navigation bar
    └── ajax/
        ├── mark_attendance.php        # AJAX attendance marking
        ├── get_attendance_stats.php    # AJAX attendance statistics
        ├── assign_grade.php           # AJAX grade assignment
        ├── export_report.php          # Report export handler
        └── update_status.php          # Status update handler
```

## Features Detail

### Public Website
- **Hero Slider** - Auto-rotating with manual navigation, dots, and keyboard controls
- **Dark Mode** - Toggle with localStorage persistence
- **Animated Counters** - Stats count up when scrolled into view (Intersection Observer)
- **Scroll Animations** - Fade-in effects on scroll
- **Leadership Profiles** - Modal popups for university leadership
- **Video Modal** - Embedded YouTube video player overlay
- **Partner Slider** - Infinite auto-scrolling partner logos
- **Contact Form** - Client-side validation with form submission
- **Back to Top** - Floating button for smooth scroll to top
- **Responsive** - Fully adaptive across desktop, tablet, and mobile

### Admin Panel
- **Role-based access** - Super admin, admin, staff roles
- **CRUD operations** - Full create, read, update, delete on all entities
- **Search & filter** - Search students, faculty, courses by various fields
- **AJAX operations** - Mark attendance, assign grades, export reports without page reload
- **Data visualization** - Chart.js integration for enrollment and attendance charts
- **Reports** - Generate summary reports, filter by date range, export capabilities
- **Status management** - Active/Inactive/Graduated/Suspended for students, Published/Draft/Archived for content

## Development

```bash
# Serve locally with live reload (if you have live-server)
npx live-server --port=8000

# Or use PHP server
php -S localhost:8000
```

To modify the public website styles, edit the CSS variables in `css/style.css`:
```css
:root {
    --primary-color: #1a5f23;
    --secondary-color: #f4a261;
    --accent-color: #2a9d8f;
}
```

## Security

- Passwords hashed with `password_hash()` (bcrypt)
- Prepared statements for SQL queries
- Session-based authentication
- Input sanitization with `htmlspecialchars()`
- Role-based access control in admin panel

## License

This project is for educational purposes. All original content, logos, and images belong to Injibara University.

---

**Built with** - HTML, CSS, JavaScript, PHP, MySQL
