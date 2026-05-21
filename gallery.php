<?php
// gallery.php
require_once 'config.php';

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Check database connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed.");
}

// Fetch contact info
$sql_contact = "SELECT address, phone_numbers, email, office_hours, whatsapp_number FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
$contact = $result_contact && $result_contact->num_rows > 0 ? $result_contact->fetch_assoc() : [
    'address' => '123 Main Street, Children\'s Corner, Kolkata, West Bengal 700001',
    'phone_numbers' => '+91-9876543210, +91-9876543211',
    'email' => 'info@childrenscorner.org',
    'office_hours' => 'Monday - Saturday: 8AM - 8PM, Sunday: 10AM - 4PM, Holidays: Closed',
    'whatsapp_number' => '+91-9876543210'
];
if (!$result_contact) {
    error_log("Contact info query error: " . $conn->error);
}

// Fetch gallery images
$sql_gallery = "SELECT title, url, category FROM gallery ORDER BY created_at DESC";
$result_gallery = $conn->query($sql_gallery);
$gallery_items = [];
if ($result_gallery) {
    while ($row = $result_gallery->fetch_assoc()) {
        $gallery_items[] = $row;
    }
} else {
    error_log("Gallery query error: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title> Gallery – Children’s Corner Institution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Noto+Sans+Bengali:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS Variables for consistent styling */
        :root {
            --background: #f8fafc; /* Page background */
            --foreground: #ffffff; /* Card and section background */
            --accent: #3a0ca3; /* Primary color */
            --accent-hover: #2575fc; /* Hover color */
            --muted: #e2e8f0; /* Muted text color */
            --border: #d1d5db; /* Border color */
            --radius: 0.75rem; /* Border radius */
            --text: #1e293b; /* Primary text color */
            --transition: all 0.3s ease; /* Smooth transitions */
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Card shadow */
            --shadow-light: 0 4px 10px rgba(0, 0, 0, 0.05); /* Light shadow */
            --gradient-primary: linear-gradient(135deg, #4361ee, #3a0ca3); /* Gradient for buttons and headers */
            --gradient-accent: linear-gradient(135deg, #6a11cb, #2575fc); /* Hover gradient */
            --font-family: 'Montserrat', 'Poppins', 'Segoe UI', Arial, sans-serif; /* Font stack */
        }

        /* Reset and base styles */
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

        /* Navbar styles */
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
            background: var(--gradient-primary);
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

        /* Breadcrumb section styles */
        .breadcrumb-section {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .breadcrumb-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://ilhaammission.org/photos/aa.png') center/cover no-repeat;
            opacity: 0.3;
            z-index: 0;
        }

        .breadcrumb-content {
            position: relative;
            z-index: 1;
        }

        .breadcrumb-item a {
            color: #ffffff;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        /* General section styles */
        .section {
            padding: 3rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-primary);
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

        /* Gallery styles */
        .gallery-filter {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: var(--foreground);
            border: 2px solid var(--border);
            padding: 10px 20px;
            margin: 5px;
            border-radius: var(--radius);
            font-weight: 600;
            color: var(--text);
            transition: var(--transition);
            cursor: pointer;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--gradient-accent);
            color: #ffffff;
            border-color: transparent;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .gallery-item {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
            cursor: pointer;
            height: 250px;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-image {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 20px;
            color: #ffffff;
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .gallery-category {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Contact section styles */
        .contact-section {
            background: var(--foreground);
            padding: 2rem;
            border-radius: var(--radius);
            text-align: center;
        }

        .contact-section h3 {
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }

        .contact-section p {
            font-size: 0.9rem;
            color: var(--text); /* Fixed for readability */
        }

        /* Modal styles */
        .modal-content {
            border-radius: var(--radius);
            overflow: hidden;
            border: none;
        }

        .modal-header {
            background: var(--gradient-primary);
            color: #ffffff;
            border-bottom: none;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body img {
            border-radius: var(--radius);
        }

        /* Footer styles */
        .footer {
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            color: white;
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

        /* Back-to-top button */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
            z-index: 1000;
        }

        .back-to-top.show {
            opacity: 1;
        }

        .back-to-top:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            animation: animate__pulse 0.5s;
        }

        /* Logo animation */
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
            0% { transform: scale(1) rotate(0deg); }
            40% { transform: scale(1.1) rotate(360deg); }
            60% { transform: scale(1.1) rotate(360deg); }
            100% { transform: scale(1) rotate(0deg); }
        }

        .academy-logo:hover {
            animation-play-state: paused;
            transform: scale(1.05);
            box-shadow: var(--shadow);
        }

        /* Decorative shapes */
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

        /* Responsive design */
        @media (max-width: 992px) {
            .section {
                padding: 2rem 0;
            }
            .section-title h2 {
                font-size: 2rem;
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
            .breadcrumb-content h1 {
                font-size: 2rem;
            }
            .breadcrumb-content p {
                font-size: 1rem;
            }
            .section-title h2 {
                font-size: 2rem;
            }
            .section-title p {
                font-size: 1rem;
            }
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            .filter-btn {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
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
            .contact-section h3 {
                font-size: 1.25rem;
            }
            .contact-section p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .breadcrumb-content h1 {
                font-size: 1.75rem;
            }
            .breadcrumb-content p {
                font-size: 0.9rem;
            }
            .section-title h2 {
                font-size: 1.75rem;
            }
            .section-title p {
                font-size: 0.9rem;
            }
            .navbar-brand {
                font-size: 1.25rem;
            }
            .navbar-brand .logo-title {
                font-size: 1.1rem;
            }
            .navbar-brand .logo-tagline {
                font-size: 0.7rem;
            }
            .academy-logo {
                width: 80px;
                height: 80px;
            }
            .shape {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
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
                    <li class="nav-item"><a class="nav-link" href="index">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="notice">Notices</a></li>
                    <li class="nav-item"><a class="nav-link" href="admission">Admission</a></li>
                    <li class="nav-item"><a class="nav-link active" href="gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="donation">Donate</a></li>
                    <li class="nav-item"><a class="nav-link" href="success">Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="activities">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty">faculty</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Section -->
    <section class="breadcrumb-section animate__animated" data-animate="fadeIn">
        <div class="container breadcrumb-content">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item animate__animated" data-animate="bounceIn" data-delay="0.1s"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active animate__animated" data-animate="bounceIn" data-delay="0.2s" aria-current="page">Gallery</li>
                </ol>
            </nav>
            <h1 class="animate__animated" data-animate="fadeInDown"> Our Gallery</h1>
            <p class="lead mb-0 animate__animated" data-animate="fadeInUp">Explore our campus, facilities, and student life</p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Photo Gallery</h2>
                <div class="title-divider"></div>
                <p>Take a visual tour of our academy and facilities</p>
            </div>
            <div class="gallery-filter animate__animated" data-animate="fadeInUp">
                <div class="filter-btn active animate__animated" data-animate="fadeInUp" data-delay="0.1s" data-filter="all">All</div>
                <div class="filter-btn animate__animated" data-animate="fadeInUp" data-delay="0.2s" data-filter="classroom">Classrooms</div>
                <div class="filter-btn animate__animated" data-animate="fadeInUp" data-delay="0.3s" data-filter="library">Library</div>
                <div class="filter-btn animate__animated" data-animate="fadeInUp" data-delay="0.4s" data-filter="hostel">Hostel</div>
                <div class="filter-btn animate__animated" data-animate="fadeInUp" data-delay="0.5s" data-filter="lab">Labs</div>
                <div class="filter-btn animate__animated" data-animate="fadeInUp" data-delay="0.6s" data-filter="event">Events</div>
            </div>
            <div class="gallery-grid" id="galleryGrid">
                <?php if (empty($gallery_items)): ?>
                    <p class="animate__animated" data-animate="fadeIn">No images found.</p>
                <?php else: ?>
                    <?php $delay = 0.1; ?>
                    <?php foreach ($gallery_items as $item): ?>
                        <div class="gallery-item animate__animated" data-animate="fadeInUp" data-delay="<?php echo $delay; ?>s" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                            <img src="<?php echo htmlspecialchars($item['url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="gallery-image">
                            <div class="gallery-overlay">
                                <div class="gallery-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                <div class="gallery-category"><?php echo htmlspecialchars(ucfirst($item['category'])); ?></div>
                            </div>
                        </div>
                        <?php $delay += 0.1; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section animate__animated" data-animate="fadeIn">
        <div class="container">
            <h3>Contact Us</h3>
            <p class="animate__animated" data-animate="fadeInUp">For any queries, please reach out to us:</p>
            <p class="animate__animated" data-animate="fadeInUp" data-delay="0.1s"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($contact['address']); ?></p>
            <p class="animate__animated" data-animate="fadeInUp" data-delay="0.2s"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($contact['phone_numbers']); ?></p>
            <p class="animate__animated" data-animate="fadeInUp" data-delay="0.3s"><i class="fas fa-envelope me-2"></i><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></p>
            <p class="animate__animated" data-animate="fadeInUp" data-delay="0.4s"><i class="fas fa-whatsapp me-2"></i><a href="https://wa.me/<?php echo htmlspecialchars(str_replace('+', '', $contact['whatsapp_number'])); ?>" target="_blank"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></a></p>
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
                        <li class="mb-3 animate__animated" data-animate="fadeInUp" data-delay="0.5s"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/<?php echo htmlspecialchars(str_replace('+', '', $contact['whatsapp_number'])); ?>" target="_blank"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="animate__animated" data-animate="fadeInUp">&copy; 2025 Children's Corner Institution & Ilhaam Mission. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top animate__animated" data-animate="bounceIn"><i class="fas fa-chevron-up"></i></a>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animate__animated" data-animate="zoomIn">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="" class="img-fluid" id="modalImage">
                </div>
            </div>
        </div>
    </div>

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

        // Initialize gallery events (filter and modal)
        function initializeGalleryEvents() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    const filterValue = button.getAttribute('data-filter');
                    galleryItems.forEach(item => {
                        if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            galleryItems.forEach(item => {
                item.addEventListener('click', () => {
                    const imageSrc = item.querySelector('.gallery-image').getAttribute('src');
                    const imageTitle = item.querySelector('.gallery-title').textContent;
                    modalImage.setAttribute('src', imageSrc);
                    modalTitle.textContent = imageTitle;
                    imageModal.show();
                });
            });
        }

        // Back to top button
        document.addEventListener('DOMContentLoaded', () => {
            handleAnimations();
            initializeGalleryEvents();

            const backToTop = document.querySelector('.back-to-top');
            if (backToTop) {
                window.addEventListener('scroll', () => {
                    backToTop.classList.toggle('show', window.pageYOffset > 300);
                });
                backToTop.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>
</body>
</html>