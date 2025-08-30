<?php
require_once 'includes/functions.php';

// Fetch visible projects
$projects = getAllVisibleProjects();

// Handle contact form submission
$contact_success = '';
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $contact_error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_error = "Invalid email address.";
    } else {
        $data = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        if (addContactMessage($data)) {
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['REQUEST_URI'] . '?success=1');
            exit();
        } else {
            $contact_error = "Failed to send message. Try again.";
        }
    }
}

// Check if we should show success message via GET parameter (after redirect)
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $contact_success = "Message sent successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Website</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <header class="header">
        <a href="#" class="logo"><span>Tushar Kumar</span></a>
        <ul class="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#experiences">Experience</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="fa-solid fa-bars" id="menu-icon"></i>
        <a href="https://github.com/tusharkumarroy" class="visit-btn" target="_blank">Visit Github</a>
    </header>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="about-container">
            <div class="image-wrapper">
                <img src="assets/images/quiet_blue.jpg" alt="profile_photo">
            </div>
            <div class="info-box">
                <div class="text">
                    <h3>Hi, I'm</h3>
                    <h1>Tushar Kumar Roy</h1>
                    <span>Full Stack Web Developer</span>
                </div>
                <div class="btn-group">
                    <a href="assets/files/CV.pdf" class="btn" download>Download CV</a>
                    <a href="#contact" class="btn">Contact</a>
                </div>
                <div class="social-handles">
                    <a href="https://github.com/TusharKumarRoy"><i class="fa-brands fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/tushar-kumar-roy-b53b541b7/"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="https://wa.me/+8801319142561/"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/tusharkumar.roy71/"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Experiences Section -->
    <section id="experiences" class="experiences">
        <h2 class="section-title">Experiences</h2>
        <div class="experiences-info">
            <div class="grid">
                <div class="grid-cards">
                    <i class="fa-brands fa-html5"></i>
                    <span>Frontend Developer</span>
                    <h3>1 year</h3>
                    <p>Worked on responsive websites using HTML, CSS, JS.</p>
                </div>
                <div class="grid-cards">
                    <i class="fa-solid fa-code"></i>
                    <span>Backend Developer</span>
                    <h3>1 year</h3>
                    <p>Built APIs and server-side applications with PHP and Node.js.</p>
                </div>
                <div class="grid-cards">
                    <i class="fa-solid fa-database"></i>
                    <span>Database Manager</span>
                    <h3>1 year</h3>
                    <p>Managed MySQL and MongoDB databases for web projects.</p>
                </div>
                <div class="grid-cards">
                    <i class="fa-solid fa-gamepad"></i>
                    <span>Game Developer</span>
                    <h3>1 year</h3>
                    <p>Created simple 2D games using Unity and C#.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="projects">
        <h2 class="section-title">Recent Projects</h2>
        <div class="projects-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="projects-card">
                        <div class="project-image">
                            <img src="assets/images/<?php echo htmlspecialchars($project['image_path']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        </div>
                        <div class="project-content">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p><?php echo htmlspecialchars($project['short_description']); ?></p>
                            <div class="btn-group">
                                <?php if(!empty($project['project_url'])): ?>
                                    <a href="<?php echo $project['project_url']; ?>" target="_blank" class="btn">Live Demo</a>
                                <?php endif; ?>
                                <?php if(!empty($project['github_url'])): ?>
                                    <a href="<?php echo $project['github_url']; ?>" target="_blank" class="btn">Github Repo</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-projects">
                    <p>No projects added yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <h2 class="section-title">Contact Me</h2>
        
        <!-- Success/Error Messages -->
        <?php if($contact_success): ?>
            <div class="alert alert-success" id="success-alert">
                <i class="fa-solid fa-check-circle"></i>
                <?php echo htmlspecialchars($contact_success); ?>
            </div>
        <?php elseif($contact_error): ?>
            <div class="alert alert-error" id="error-alert">
                <i class="fa-solid fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($contact_error); ?>
            </div>
        <?php endif; ?>
        
        <div class="contact-container">
            <form method="POST" class="contact-form" id="contactForm">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" required value="<?php echo isset($_POST['name']) && $contact_error ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email" required value="<?php echo isset($_POST['email']) && $contact_error ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="subject" placeholder="Subject" required value="<?php echo isset($_POST['subject']) && $contact_error ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>
                <div class="form-group">
                    <textarea name="message" rows="5" placeholder="Message" required><?php echo isset($_POST['message']) && $contact_error ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
                <button type="submit" name="submit_contact" value="1" class="btn submit-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </section>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <a href="#" class="mobile-logo">Tushar Kumar</a>
                <i class="fa-solid fa-times" id="close-menu"></i>
            </div>
            <ul class="mobile-nav-links">
                <li><a href="#about">About</a></li>
                <li><a href="#experiences">Experience</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <a href="https://github.com/tusharkumarroy" class="mobile-visit-btn" target="_blank">Visit Github</a>
        </div>
    </div>

    <!-- JS -->
    <script src="assets/js/script.js"></script>
    <script src="/assets/js/shortcut_toggle.js"></script>
</body>

</html>