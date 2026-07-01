// Admin Panel JavaScript

// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    
    // For mobile
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
    }
}

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && window.innerWidth <= 768) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
});

// Confirm Delete
function confirmDelete(id, type) {
    if (confirm('Are you sure you want to delete this ' + type + '?')) {
        window.location.href = 'delete.php?type=' + type + '&id=' + id;
    }
}

// Image Preview
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Search Table
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        tr[i].style.display = found ? '' : 'none';
    }
}

// Toggle Status
function toggleStatus(id, type) {
    fetch('ajax/toggle_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&type=' + type
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status');
        }
    });
}

// Print Table
function printTable(tableId) {
    const table = document.getElementById(tableId);
    const newWin = window.open('');
    newWin.document.write('<html><head><title>Print</title>');
    newWin.document.write('<link rel="stylesheet" href="css/admin.css">');
    newWin.document.write('</head><body>');
    newWin.document.write(table.outerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
}

// Export Table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    const csv = [];
    
    for (const row of rows) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        for (const col of cols) {
            rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        }
        csv.push(rowData.join(','));
    }
    
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Mark Attendance
function markAttendance(studentId, courseId, status) {
    fetch('ajax/mark_attendance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'student_id=' + studentId + '&course_id=' + courseId + '&status=' + status
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Attendance marked successfully!');
        } else {
            alert('Error marking attendance');
        }
    });
}

// Grade Assignment
function assignGrade(studentCourseId, grade) {
    fetch('ajax/assign_grade.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'student_course_id=' + studentCourseId + '&grade=' + grade
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Grade assigned successfully!');
            location.reload();
        } else {
            alert('Error assigning grade');
        }
    });
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const required = form.querySelectorAll('[required]');
    let valid = true;
    
    required.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            valid = false;
        } else {
            field.style.borderColor = '#ddd';
        }
    });
    
    return valid;
}

// Date Range Picker
function filterByDate() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    
    if (start && end) {
        window.location.href = '?start=' + start + '&end=' + end;
    }
}

// Notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
