<?php
// notice.php
require_once dirname(__DIR__, 3) . '/config.php';

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__, 3) . '/storage/logs/php-error.log');

// Fetch notices from database
$sql_notifications = "SELECT title, text, date, link, type, new_duration FROM notifications WHERE status = 'active' ORDER BY date DESC";
$result_notifications = $conn->query($sql_notifications);
$notifications = [];
if ($result_notifications) {
    while ($row = $result_notifications->fetch_assoc()) {
        $notifications[] = $row;
    }
} else {
    error_log('Notifications query failed: ' . $conn->error);
}

// Fetch contact information for footer
$sql_contact = "SELECT * FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
if (!$result_contact) {
    error_log('Contact query failed: ' . $conn->error);
    $contact = ['phone_numbers' => '9091850061, 9163588008', 'email' => 'childrenscorner85@gmail.com', 'whatsapp_number' => '9163588008', 'address' => 'Sangrampur, Kalikapota, West Bengal 743355', 'office_hours' => 'Mon - Sat: 9 AM - 5 PM'];
} else {
    $contact = $result_contact->fetch_assoc() ?: ['phone_numbers' => '9091850061, 9163588008', 'email' => 'childrenscorner85@gmail.com', 'whatsapp_number' => '9163588008', 'address' => 'Sangrampur, Kalikapota, West Bengal 743355', 'office_hours' => 'Mon - Sat: 9 AM - 5 PM'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Notices – Ilhaam Mission</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Dancing+Script:wght@700&family=Noto+Sans+Bengali:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --background: #f8fafc;
            --foreground: #ffffff;
            --accent: #3a0ca3;
            --accent-hover: #2575fc;
            --muted: #e2e8f0;
            --border: #d1d5db;
            --radius: 0.75rem;
            --text: #1e293b;
            --transition: all 0.3s ease;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --gradient-primary: linear-gradient(135deg, #4361ee, #3a0ca3);
            --gradient-accent: linear-gradient(135deg, #6a11cb, #2575fc);
            --shadow-light: 0 4px 10px rgba(0, 0, 0, 0.05);
            --font-family: 'Montserrat', 'Poppins', 'Segoe UI', Arial, sans-serif;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font-family);
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            scroll-behavior: smooth;
            min-height: 100vh;
        }
        a {
            text-decoration: none;
            color: var(--accent);
            transition: var(--transition);
        }
        a:hover {
            color: var(--accent-hover);
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            padding: 1.5rem 0;
            transition: var(--transition);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 800;
            font-size: 1.7rem;
            color: var(--text);
        }
        .navbar-brand .logo-text {
            margin-left: 1rem;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand .logo-title {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .navbar-brand .logo-tagline {
            font-size: 0.9rem;
            color: var(--text);
            font-weight: 400;
        }
        .nav-link {
            color: var(--text) !important;
            font-weight: 600;
            transition: var(--transition);
            margin: 0 0.5rem;
            position: relative;
            font-size: clamp(0.9rem, 3vw, 1rem);
            padding: 8px 12px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -2px;
            left: 0;
            background: var(--gradient-accent);
            transition: var(--transition);
            border-radius: var(--radius);
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link:hover {
            color: var(--accent) !important;
        }
        .nav-link[data-animate-hover]:hover {
            animation: animate__tada 0.5s;
        }
        .section {
            padding: 3rem 0;
            background-color: var(--background);
        }
        .section-title {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .section-title p {
            font-size: 1.1rem;
            color: var(--text);
            max-width: 600px;
            margin: 0 auto;
        }
        .title-divider {
            height: 5px;
            width: 100px;
            background: var(--gradient-accent);
            margin: 1rem auto;
            border-radius: var(--radius);
        }
        .card {
            background: var(--foreground);
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-light);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            transition: var(--transition);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        .card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }
        .card p {
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .card .date {
            font-size: 0.85rem;
            color: var(--text);
            font-style: italic;
        }
        .card .btn {
            background: var(--gradient-accent);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
            transition: var(--transition);
        }
        .card .btn:hover {
            background: var(--gradient-primary);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }
        .new-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--gradient-accent);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius);
        }
        .footer {
            background: var(--gradient-accent);
            color: #ffffff;
            padding: 3rem 0 1rem;
        }
        .footer a {
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
        }
        .footer a:hover {
            color: #ffffff;
        }
        .footer h5 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: var(--transition);
        }
        .social-icons a:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            animation: animate__pulse 0.5s;
        }
        .whatsapp_float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            border-radius: 50%;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 9999;
            transition: all 0.3s ease;
        }
        .whatsapp_float img {
            width: 35px;
            margin-top: 12px;
        }
        .whatsapp_float:hover {
            transform: scale(1.1);
            background-color: #20b858;
        }
        .academy-logo {
            width: 96px;
            height: 96px;
            object-fit: contain;
            border-radius: var(--radius);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: var(--foreground);
            padding: 8px;
        }
        @keyframes rotateLogo {
            0% {
                transform: scale(1) rotate(0deg);
            }
            40% {
                transform: scale(1.1) rotate(360deg);
            }
            60% {
                transform: scale(1.1) rotate(360deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }
        .academy-logo:hover {
            animation-play-state: paused;
            transform: scale(1.05);
            box-shadow: var(--shadow);
        }
        .shape {
            position: absolute;
            z-index: -1;
            pointer-events: none;
        }
        .shape-1 {
            top: 10%;
            left: 2%;
            width: 80px;
            height: 80px;
            background: var(--gradient-accent);
            opacity: 0.08;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: morphing 12s infinite linear;
        }
        .shape-2 {
            top: 50%;
            right: 2%;
            width: 120px;
            height: 120px;
            background: var(--gradient-accent);
            opacity: 0.08;
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            animation: morphing 10s infinite linear;
        }
        @keyframes morphing {
            0% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
            100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        }
        @media (max-width: 992px) {
            .section {
                padding: 2rem 0;
            }
            .section-title h2 {
                font-size: 2rem;
            }
            .section-title p {
                font-size: 1rem;
            }
            .navbar-nav {
                text-align: center;
            }
            .nav-link {
                padding: 6px 10px;
            }
            .navbar-brand .logo-title {
                font-size: 1.3rem;
            }
            .navbar-brand .logo-tagline {
                font-size: 0.8rem;
            }
            .academy-logo {
                width: 80px;
                height: 80px;
            }
        }
        @media (max-width: 768px) {
            .section-title h2 {
                font-size: 2rem;
            }
            .section-title p {
                font-size: 1rem;
            }
            .navbar-brand .logo-title {
                font-size: 1.3rem;
            }
            .navbar-brand .logo-tagline {
                font-size: 0.8rem;
            }
            .nav-link {
                margin: 5px 0;
                font-size: clamp(0.85rem, 2.8vw, 0.95rem);
            }
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            .nav-link {
                font-size: 0.9rem;
                margin: 0.3rem 0;
            }
            .section-title h2 {
                font-size: 1.75rem;
            }
            .section-title p {
                font-size: 0.9rem;
            }
            .academy-logo {
                width: 80px;
                height: 80px;
            }
            .navbar-brand .logo-title {
                font-size: 1.1rem;
            }
            .navbar-brand .logo-tagline {
                font-size: 0.7rem;
            }
            .shape {
                display: none;
            }
            .card { padding: 1rem; }
            .card h4 { font-size: 1.1rem; }
            .card p, .card .date, .card .btn { font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top animate__animated" data-animate="fadeIn">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="logo-container">
                    <img src="https://ilhaammission.org/photos/logo.png" class="academy-logo" alt="Ilhaam Mission Logo" loading="lazy">
                    <div class="logo-text">
                        <span class="logo-title">ILHAAM MISSION</span>
                        <span class="logo-tagline">Empowering Young Minds for a Bright Future</span>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index" data-animate-hover="tada">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about" data-animate-hover="tada">About Us</a></li>
                    <li class="nav-item"><a class="nav-link active" href="notice" data-animate-hover="tada">Notices</a></li>
                    <li class="nav-item"><a class="nav-link" href="admission" data-animate-hover="tada">Admission</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery" data-animate-hover="tada">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="donation" data-animate-hover="tada">Donate</a></li>
                    <li class="nav-item"><a class="nav-link" href="success" data-animate-hover="tada">Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="activities" data-animate-hover="tada">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty" data-animate-hover="tada">faculty</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact" data-animate-hover="tada">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Notices Section -->
    <section class="section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Notices – Ilhaam Mission</h2>
                <div class="title-divider"></div>
                <p>Stay updated with the latest announcements and updates from Ilhaam Mission</p>
            </div>
            <div class="row">
                <?php if (!empty($notifications)): ?>
                    <?php
                    $currentDate = new DateTime();
                    foreach ($notifications as $notification):
                        $notificationDate = new DateTime($notification['date']);
                        $interval = $currentDate->diff($notificationDate);
                        $isNew = $interval->days <= ($notification['new_duration'] ?: 7);
                    ?>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="card animate__animated" data-animate="fadeInUp">
                            <?php if ($isNew): ?>
                                <span class="new-badge">New</span>
                            <?php endif; ?>
                            <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                            <p><?php echo htmlspecialchars($notification['text']); ?></p>
                            <div class="date">Posted on: <?php echo htmlspecialchars($notification['date']); ?></div>
                            <?php if (!empty($notification['link'])): ?>
                                <a href="<?php echo htmlspecialchars($notification['link']); ?>" 
                                   class="btn" 
                                   <?php echo $notification['type'] === 'pdf' ? 'download' : 'target="_blank"'; ?>>
                                    <i class="fas <?php echo $notification['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-link'; ?> me-2"></i>
                                    <?php echo $notification['type'] === 'pdf' ? 'Download PDF' : 'View Details'; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No notices available at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer animate__animated" data-animate="fadeInUp">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="animate__animated" data-animate="fadeIn">About Us</h5>
                    <p class="animate__animated" data-animate="fadeInUp" data-delay="0.1s">Children’s Corner Institution X Ilhaam Mission is dedicated to the holistic development of vulnerable and marginalized populations, ensuring success through education, health, and livelihood initiatives.</p>
                    <div class="social-icons">
                        <a href="https://facebook.com/childrenscorner" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com/childrenscorner" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com/childrenscorner" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-twitter"></i></a>
                        <a href="https://youtube.com/childrenscorner" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-youtube"></i></a>
                        <a href="https://linkedin.com/company/childrenscorner" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="animate__animated" data-animate="fadeIn">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="animate__animated" data-animate-hover="pulse">Home</a></li>
                        <li><a href="about.php" class="animate__animated" data-animate-hover="pulse">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="animate__animated" data-animate="fadeIn">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.1s"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($contact['address']); ?></li>
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.2s"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($contact['phone_numbers']); ?></li>
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.3s"><i class="fas fa-envelope me-2"></i><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></li>
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.4s"><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($contact['office_hours']); ?></li>
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.5s"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/<?php echo htmlspecialchars($contact['whatsapp_number']); ?>" target="_blank"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="animate__animated" data-animate="fadeInUp">&copy; 2025 Children's Corner Institution & Ilhaam Mission. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/<?php echo htmlspecialchars($contact['whatsapp_number']); ?>" target="_blank" class="whatsapp_float" title="Chat with us on WhatsApp">
        <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp" />
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation Handler
        function handleAnimations() {
            const elements = document.querySelectorAll('[data-animate]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animation = element.getAttribute('data-animate');
                        const delay = element.getAttribute('data-delay') || '0s';
                        element.classList.add('animate__animated', 'animate__' + animation);
                        element.style.animationDelay = delay;
                        observer.unobserve(element);
                    }
                });
            }, { threshold: 0.2 });

            elements.forEach(element => observer.observe(element));

            // Handle hover animations
            const hoverElements = document.querySelectorAll('[data-animate-hover]');
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', () => {
                    const animation = element.getAttribute('data-animate-hover');
                    element.classList.add('animate__animated', 'animate__' + animation);
                    element.addEventListener('animationend', () => {
                        element.classList.remove('animate__animated', 'animate__' + animation);
                    }, { once: true });
                });
            });
        }

        // Initialize animations on page load
        document.addEventListener('DOMContentLoaded', () => {
            handleAnimations();
        });
    </script>
</body>
</html>
