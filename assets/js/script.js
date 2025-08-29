// Mobile Menu Toggle
const menuIcon = document.getElementById('menu-icon');
const closeMenu = document.getElementById('close-menu');
const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a');

// Open mobile menu
menuIcon.addEventListener('click', () => {
    mobileMenuOverlay.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling
    
    // Add animation
    setTimeout(() => {
        mobileMenuOverlay.style.opacity = '1';
    }, 10);
});

// Close mobile menu
closeMenu.addEventListener('click', closeMobileMenu);

// Close mobile menu when clicking on overlay
mobileMenuOverlay.addEventListener('click', (e) => {
    if (e.target === mobileMenuOverlay) {
        closeMobileMenu();
    }
});

// Close mobile menu when clicking on navigation links
mobileNavLinks.forEach(link => {
    link.addEventListener('click', closeMobileMenu);
});

// Close mobile menu function
function closeMobileMenu() {
    mobileMenuOverlay.style.opacity = '0';
    document.body.style.overflow = 'auto'; // Restore scrolling
    
    setTimeout(() => {
        mobileMenuOverlay.style.display = 'none';
    }, 300);
}

// Close mobile menu on window resize if screen becomes larger
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeMobileMenu();
    }
});

// Smooth scrolling for navigation links
const navLinks = document.querySelectorAll('a[href^="#"]');

navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        
        // Don't prevent default for external links or just "#"
        if (href === '#' || href.startsWith('http')) {
            return;
        }
        
        e.preventDefault();
        
        const targetId = href.substring(1);
        const targetSection = document.getElementById(targetId);
        
        if (targetSection) {
            let targetPosition;
            
            // Special handling for contact section - scroll to bottom
            if (targetId === 'contact') {
                targetPosition = document.body.scrollHeight;
            } else {
                const headerHeight = document.querySelector('.header').offsetHeight + 20;
                targetPosition = targetSection.offsetTop - headerHeight;
            }
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// Auto-hide success/error alerts
document.addEventListener('DOMContentLoaded', () => {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');
    
    // Auto-hide success message after 10 seconds
    if (successAlert) {
        setTimeout(() => {
            hideAlert(successAlert);
        }, 10000);
    }
    
    // Auto-hide error message after 5 seconds
    if (errorAlert) {
        setTimeout(() => {
            hideAlert(errorAlert);
        }, 5000);
    }
    
    // Add click to dismiss functionality
    [successAlert, errorAlert].forEach(alert => {
        if (alert) {
            alert.style.cursor = 'pointer';
            alert.title = 'Click to dismiss';
            alert.addEventListener('click', () => {
                hideAlert(alert);
            });
        }
    });
    
    // Clean URL after showing success message
    if (window.location.search.includes('success=1')) {
        // Remove the success parameter from URL after a short delay
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url.pathname + url.hash);
        }, 100);
    }
});

// Hide alert function with animation
function hideAlert(alert) {
    if (!alert) return;
    
    alert.style.animation = 'fadeOut 0.3s ease-in-out forwards';
    
    setTimeout(() => {
        alert.remove();
    }, 300);
}

// Form validation and submission handling
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        const name = contactForm.querySelector('input[name="name"]').value.trim();
        const email = contactForm.querySelector('input[name="email"]').value.trim();
        const subject = contactForm.querySelector('input[name="subject"]').value.trim();
        const message = contactForm.querySelector('textarea[name="message"]').value.trim();
        
        // Basic client-side validation
        if (!name || !email || !subject || !message) {
            e.preventDefault();
            showAlert('Please fill in all fields.', 'error');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showAlert('Please enter a valid email address.', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = contactForm.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
        
        // Prevent multiple submissions
        contactForm.style.pointerEvents = 'none';
        
        // Re-enable form after timeout (in case of server issues)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            contactForm.style.pointerEvents = 'auto';
        }, 10000); // 10 seconds timeout
    });
    
    // Reset form completely on successful submission redirect
    if (window.location.search.includes('success=1')) {
        contactForm.reset();
    }
}

// Show alert function for client-side messages
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.id = `${type}-alert`;
    alert.innerHTML = `
        <i class="fa-solid fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
        ${message}
    `;
    
    // Insert before contact form
    const contactContainer = document.querySelector('.contact-container');
    contactContainer.parentNode.insertBefore(alert, contactContainer);
    
    // Auto-hide after appropriate time
    const hideTime = type === 'success' ? 2000 : 5000;
    setTimeout(() => {
        hideAlert(alert);
    }, hideTime);
    
    // Add click to dismiss
    alert.style.cursor = 'pointer';
    alert.title = 'Click to dismiss';
    alert.addEventListener('click', () => {
        hideAlert(alert);
    });
}


// Intersection Observer for animations (optional enhancement)
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', () => {
    const animateElements = document.querySelectorAll('.grid-cards, .projects-card');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
});

// Add fadeInUp animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Prevent form resubmission on page refresh and handle browser back/forward
window.addEventListener('pageshow', (event) => {
    // Reset form if coming from cache (back/forward navigation)
    if (event.persisted) {
        const form = document.getElementById('contactForm');
        if (form) {
            form.reset();
        }
    }
});

// Handle browser navigation and form state
window.addEventListener('popstate', () => {
    const form = document.getElementById('contactForm');
    if (form) {
        form.reset();
    }
});

// Prevent duplicate submissions by clearing form data from memory
if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
    const form = document.getElementById('contactForm');
    if (form) {
        form.reset();
    }
}

// Admin dashboard shortcut
document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === ".") {
        window.location.href = "http://localhost/Full%20Stack%20Projects/portfolio_website/admin/dashboard.php";
    }
});