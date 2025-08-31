# portfolio_website
Personal Portfolio Website With Admin Panel


<?php
require_once 'includes/send_mail.php';
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
        // Save to database first
        $data = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        $db_saved = addContactMessage($data);
        $mail_sent = sendContactEmail($name, $email, $subject, $message);  // USE PHPMailer FUNCTION

        if ($db_saved && $mail_sent) {
            header('Location: ' . $_SERVER['REQUEST_URI'] . '?success=1');
            exit();
        } elseif ($db_saved) {
            $contact_error = "Message saved but email failed to send.";
        } else {
            $contact_error = "Failed to save message.";
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

----------

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