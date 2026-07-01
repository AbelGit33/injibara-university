// ==========================================
// INJIBARA UNIVERSITY REPLICA - ENHANCED JS
// ==========================================

// Loading Screen
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    setTimeout(() => {
        loader.classList.add('hidden');
        // Trigger initial animations
        animateOnScroll();
        startCounters();
    }, 1000);
});

// Dark Mode Toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const icon = document.querySelector('#darkModeToggle i');
    if (document.body.classList.contains('dark-mode')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
    }
}

// Check saved theme
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    document.querySelector('#darkModeToggle i').classList.remove('fa-moon');
    document.querySelector('#darkModeToggle i').classList.add('fa-sun');
}

// Hero Slider
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    if (index >= slides.length) index = 0;
    if (index < 0) index = slides.length - 1;

    slides[index].classList.add('active');
    dots[index].classList.add('active');
    currentSlideIndex = index;
}

function changeSlide(direction) {
    showSlide(currentSlideIndex + direction);
}

function currentSlide(index) {
    showSlide(index - 1);
}

// Auto slide
let slideInterval = setInterval(() => changeSlide(1), 5000);

// Pause on hover
document.querySelector('.hero-slider').addEventListener('mouseenter', () => {
    clearInterval(slideInterval);
});

document.querySelector('.hero-slider').addEventListener('mouseleave', () => {
    slideInterval = setInterval(() => changeSlide(1), 5000);
});

// Hamburger Menu
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navbar');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.querySelector('.nav-menu').classList.toggle('active');
});

// Close menu on link click
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.querySelector('.nav-menu').classList.remove('active');
    });
});

// Dropdown toggle for mobile
if (window.innerWidth <= 768) {
    document.querySelectorAll('.dropdown > .nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const dropdown = link.parentElement;
            dropdown.classList.toggle('active');
        });
    });
}

// Counter Animation
function startCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.ceil(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };

        // Start when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(counter);
    });
}

// Scroll Animations
function animateOnScroll() {
    const elements = document.querySelectorAll('.stat-item, .leader-card, .news-card, .service-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in', 'visible');
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });
}

// Back to Top Button
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }

    // Update active nav link
    updateActiveNavLink();
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Active Navigation Link
function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');

    let currentSection = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (window.scrollY >= sectionTop) {
            currentSection = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${currentSection}`) {
            link.classList.add('active');
        }
    });
}

// Search Functionality
function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    if (searchTerm.trim() === '') return;

    // Simple search implementation
    const content = document.body.innerText.toLowerCase();
    if (content.includes(searchTerm)) {
        alert(`Found "${searchTerm}" in the page. Use Ctrl+F for detailed search.`);
    } else {
        alert(`"${searchTerm}" not found on this page.`);
    }
}

// Enter key search
document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Video Modal
function openVideoModal() {
    const modal = document.getElementById('videoModal');
    const iframe = document.getElementById('videoFrame');
    iframe.src = 'https://www.youtube.com/embed/injibarauniversity';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const iframe = document.getElementById('videoFrame');
    iframe.src = '';
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Leader Modal
const leaderData = {
    'president': {
        name: 'Gardachew Worku (PhD)',
        title: 'President, Injibara University',
        subtitle: 'Associate Professor of Accounting and Finance',
        image: 'https://www.inu.edu.et/wp-content/uploads/2024/06/dr-gerdachew-scaled-2-1024x683-1.jpg',
        bio: 'Dr. Gardachew Worku is the President of Injibara University. He holds a PhD in Accounting and Finance and has been serving as an associate professor before his appointment as president. Under his leadership, the university has seen significant growth in academic programs and research initiatives.'
    },
    'academic-vp': {
        name: 'Aemiro Tadesse (PhD)',
        title: 'Academic Affairs Vice President',
        subtitle: 'Assistant Professor of Psychology',
        image: 'https://www.inu.edu.et/wp-content/uploads/2024/06/dr-amro-scaled-1-1024x548-1.jpg',
        bio: 'Dr. Aemiro Tadesse serves as the Vice President for Academic Affairs. He is an Assistant Professor of Psychology with extensive experience in higher education administration and curriculum development.'
    },
    'admin-vp': {
        name: 'Wohabe Birhan (PhD)',
        title: 'Admin & Development Vice President',
        subtitle: 'Associate Professor of Applied Developmental Psychology',
        image: 'https://www.inu.edu.et/wp-content/uploads/2024/06/ww-1024x682-1.jpg',
        bio: 'Dr. Wohabe Birhan is the Vice President for Administration and Development. He brings expertise in developmental psychology to university administration and strategic planning.'
    },
    'research-vp': {
        name: 'Kindie Birhan (PhD)',
        title: 'Research & Community Service VP',
        subtitle: 'Assistant Professor of Education',
        image: 'https://www.inu.edu.et/wp-content/uploads/2024/06/Dr-Kindie-PhD-1024x683-1.jpg',
        bio: 'Dr. Kindie Birhan leads the research and community service initiatives at Injibara University. As an Assistant Professor of Education, he focuses on promoting research excellence and community engagement.'
    }
};

function openModal(leaderId) {
    const modal = document.getElementById('leaderModal');
    const content = document.getElementById('modalContent');
    const data = leaderData[leaderId];

    content.innerHTML = `
        <div style="text-align: center;">
            <img src="${data.image}" alt="${data.name}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
            <h2 style="color: var(--primary-color); margin-bottom: 10px;">${data.name}</h2>
            <h3 style="color: var(--secondary-color); margin-bottom: 5px;">${data.title}</h3>
            <p style="color: var(--text-light); margin-bottom: 20px;">${data.subtitle}</p>
            <p style="line-height: 1.8; text-align: left;">${data.bio}</p>
        </div>
    `;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('leaderModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modals on outside click
window.addEventListener('click', (e) => {
    const videoModal = document.getElementById('videoModal');
    const leaderModal = document.getElementById('leaderModal');

    if (e.target === videoModal) closeVideoModal();
    if (e.target === leaderModal) closeModal();
});

// Form Submission
function submitForm(e) {
    e.preventDefault();

    // Get form data
    const formData = new FormData(e.target);
    const name = e.target.querySelector('input[type="text"]').value;
    const email = e.target.querySelector('input[type="email"]').value;

    // Simulate form submission
    alert(`Thank you ${name}! Your message has been received. We'll contact you at ${email} soon.`);
    e.target.reset();
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Dynamic Visitor Counter Animation
function updateVisitorCounter() {
    const counters = {
        usersToday: 136,
        usersMonth: 590,
        usersTotal: 196038,
        viewsToday: 217
    };

    Object.keys(counters).forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            const target = counters[id];
            let current = parseInt(el.textContent.replace(/,/g, ''));
            if (current < target) {
                const increment = Math.ceil((target - current) / 100);
                const update = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(update);
                    }
                    el.textContent = current.toLocaleString();
                }, 30);
            }
        }
    });
}

// Initialize visitor counter when visible
const visitorObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            updateVisitorCounter();
            visitorObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const visitorSection = document.querySelector('.footer');
if (visitorSection) {
    visitorObserver.observe(visitorSection);
}

// Partners Slider Clone for Infinite Scroll
document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.partners-track');
    if (track) {
        // Clone all children for infinite scroll
        const clone = track.innerHTML;
        track.innerHTML += clone;
    }
});

// Keyboard Navigation
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeVideoModal();
        closeModal();
    }

    if (e.key === 'ArrowLeft') changeSlide(-1);
    if (e.key === 'ArrowRight') changeSlide(1);
});

// Page Load Console Message
console.log('%c Injibara University Website Replica ', 'background: #1a5f23; color: white; font-size: 20px; padding: 10px;');
console.log('%c Enhanced with Modern Web Technologies ', 'background: #f4a261; color: white; font-size: 14px; padding: 5px;');
console.log('Features: Dark Mode, Animations, Responsive Design, Interactive Elements');
