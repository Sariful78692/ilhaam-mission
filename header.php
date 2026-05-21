<?php
// header.php
require_once 'config.php';
$sql_contact = "SELECT * FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
$contact = $result_contact->fetch_assoc();

// Get current page name for active class
$current_page = basename($_SERVER['PHP_SELF']);

// Helper function to check if current page belongs to a section
function isActive($pages) {
    global $current_page;
    return in_array($current_page, $pages) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - " : ""; ?>ILHAAM MISSION</title>

    <!-- Bootstrap 5 + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Dancing+Script:wght@700&family=Noto+Sans+Bengali:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f8fafc;
            --white: #ffffff;
            --primary: #3a0ca3;
            --primary-hover: #2575fc;
            --text: #1e293b;
            --muted: #64748b;
            --radius: 0.75rem;
            --shadow: 0 4px 20px rgba(0,0,0,0.1);
            --gradient: linear-gradient(135deg, #4361ee, #3a0ca3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            transition: all 0.3s ease;
            position: sticky;
            top: 0;
            z-index: 1050;
        }
        .navbar.scrolled {
            padding: 0.7rem 0;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .logo-img {
            width: 80px; height: 80px; object-fit: contain;
            border-radius: var(--radius); background: white; padding: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
        }
        .logo-img:hover, .logo-img:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 25px rgba(58, 12, 163, 0.3);
        }

        .logo-title {
            font-weight: 800; font-size: 1.4rem;
            background: var(--gradient); -webkit-background-clip: text; background-clip: text;
            color: transparent; display: block;
        }
        .logo-tagline { font-size: 0.8rem; color: var(--muted); font-weight: 500; }

        /* Nav Links */
        .nav-link {
            color: var(--text) !important; font-weight: 600; font-size: 0.95rem;
            padding: 0.6rem 1rem !important; border-radius: var(--radius);
            transition: all 0.3s ease; position: relative;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            background: rgba(58, 12, 163, 0.08);
        }
        .nav-link::after {
            content: ''; position: absolute; width: 0; height: 3px; bottom: 8px; left: 50%;
            background: var(--gradient); transition: all 0.3s ease; border-radius: 3px;
        }
        .nav-link:hover::after, .nav-link.active::after {
            width: 70%; left: 15%;
        }

        /* Dropdown Styling */
        .dropdown-menu {
            border: none; border-radius: var(--radius);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 0.75rem 0; min-width: 240px; margin-top: 0.5rem;
        }
        .dropdown-item {
            padding: 0.75rem 1.5rem; font-weight: 500; color: var(--text);
            transition: all 0.3s ease;
        }
        .dropdown-item:hover {
            background: var(--gradient); color: white !important;
            padding-left: 2rem;
        }
        .dropdown-item i {
            width: 20px; margin-right: 8px; opacity: 0.7;
        }

        /* Mobile */
        @media (max-width: 992px) {
            .logo-img { width: 65px; height: 65px; }
            .logo-title { font-size: 1.2rem; }
            .logo-tagline { font-size: 0.75rem; }
            .nav-link { padding: 0.8rem 1rem !important; font-size: 1rem; }
        }
        @media (max-width: 576px) {
            .logo-img { width: 55px; height: 55px; }
            .logo-title { font-size: 1.1rem; }
            .logo-tagline { font-size: 0.7rem; }
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute; pointer-events: none; opacity: 0.07; z-index: -1;
            animation: float 20s infinite linear;
        }
        .shape-1 { top: 15%; left: 5%; width: 100px; height: 100px; background: var(--gradient); border-radius: 60% 40% 30% 70%; }
        .shape-2 { top: 60%; right: 8%; width: 140px; height: 140px; background: var(--gradient); border-radius: 30% 70% 70% 30%; animation-delay: -10s; }
        @keyframes float { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(180deg); } }
    </style>
</head>
<body>

    <!-- Floating Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="https://ilhaammission.org/photos/logo.png" alt="Ilhaam Mission" class="logo-img me-2">
                <div class="logo-text">
                    <span class="logo-title">ILHAAM MISSION</span>
                    <span class="logo-tagline">Empowering Young Minds</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars fs-3 text-primary"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive(['index.php']) ?>" href="index.php">Home</a>
                    </li>

                    <!-- About Us Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActive(['about.php']) ?>" href="#" role="button" data-bs-toggle="dropdown">About Us</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php#who-we-are"><i class="fas fa-info-circle"></i> Who We Are</a></li>
                            <li><a class="dropdown-item" href="about.php#mission-vision"><i class="fas fa-bullseye"></i> Mission & Vision</a></li>
                            <li><a class="dropdown-item" href="about.php#leadership"><i class="fas fa-users"></i> Leadership Messages</a></li>
                            <li><a class="dropdown-item" href="about.php#founder"><i class="fas fa-user-tie"></i> Founder's Message</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="about.php#institutions"><i class="fas fa-building"></i> Our Institutions</a></li>
                        </ul>
                    </li>

                    <!-- Gallery Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActive(['gallery.php', 'gallery-classroom.php', 'gallery-events.php']) ?>" href="#" role="button" data-bs-toggle="dropdown">Gallery</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="gallery.php"><i class="fas fa-images"></i> All Photos</a></li>
                            <li><a class="dropdown-item" href="gallery.php?category=classroom"><i class="fas fa-chalkboard-teacher"></i> Classroom</a></li>
                            <li><a class="dropdown-item" href="gallery.php?category=events"><i class="fas fa-calendar-star"></i> Events & Programs</a></li>
                            <li><a class="dropdown-item" href="gallery.php?category=campus"><i class="fas fa-school"></i> Campus Life</a></li>
                            <li><a class="dropdown-item" href="gallery.php?category=achievements"><i class="fas fa-trophy"></i> Achievements</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a class="nav-link <?php echo isActive(['notice.php']) ?>" href="notice.php">Notices</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo isActive(['admission.php']) ?>" href="admission.php">Admission</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo isActive(['donation.php']) ?>" href="donation.php">Donate</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo isActive(['success.php']) ?>" href="success.php">Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo isActive(['activities.php']) ?>" href="activities.php">Activities</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo isActive(['faculty.php']) ?>" href="faculty.php">Faculty</a></li>

                    <!-- Contact Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActive(['contact.php']) ?>" href="#" role="button" data-bs-toggle="dropdown">Contact</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="contact.php"><i class="fas fa-envelope"></i> Get In Touch</a></li>
                            <li><a class="dropdown-item" href="contact.php#info"><i class="fas fa-address-book"></i> Contact Information</a></li>
                            <li><a class="dropdown-item" href="contact.php#map"><i class="fas fa-map-marked-alt"></i> Location Map</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar shrink on scroll
        window.addEventListener('scroll', () => {
            document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 50);
        });
    </script>
</body>
</html>