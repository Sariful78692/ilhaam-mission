<?php
ob_start();
session_start();

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once dirname(__DIR__, 3) . '/config.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__, 3) . '/storage/logs/php-error.log');
error_reporting(E_ALL);

$error_message = '';

$max_attempts = 5;
$lockout_time = 300;
$attempt_key = 'admit_attempts';
$lockout_key = 'admit_lockout';

if (!isset($_SESSION[$attempt_key])) {
    $_SESSION[$attempt_key] = 0;
}

if (isset($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > time()) {
    $error_message = 'Too many attempts. Try again in ' . ($_SESSION[$lockout_key] - time()) . ' seconds.';
    error_log("Rate limit exceeded: IP=" . $_SERVER['REMOTE_ADDR']);
} elseif ($_SESSION[$attempt_key] >= $max_attempts) {
    $_SESSION[$lockout_key] = time() + $lockout_time;
    $error_message = 'Too many attempts. Please wait 5 minutes.';
    error_log("Rate limit triggered: IP=" . $_SERVER['REMOTE_ADDR']);
}

$roll_number = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roll_number']) && empty($error_message)) {
    $_SESSION[$attempt_key]++;
    $roll_number = trim($_POST['roll_number']);
    error_log("Admit card search: IP=" . $_SERVER['REMOTE_ADDR'] . ", Roll=" . $roll_number);

    $captcha_success = (object) ['success' => true]; // reCAPTCHA disabled for testing

    if (empty($roll_number)) {
        $error_message = "Please enter a valid roll number (letters and numbers only).";
        error_log("Empty roll number submitted: IP=" . $_SERVER['REMOTE_ADDR']);
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $roll_number)) {
        $error_message = "Invalid roll number. Please use only letters and numbers.";
        error_log("Invalid roll number format: $roll_number");
    } else {
        if (!$conn) {
            $error_message = "Database connection failed. Please try again later.";
            error_log("Database connection failed: " . $conn->connect_error);
        } else {
            $stmt = $conn->prepare("SELECT pdf_url FROM admit_cards WHERE roll_number = ?");
            if (!$stmt) {
                $error_message = "Database query preparation failed.";
                error_log("Prepare failed: " . $conn->error);
            } else {
                $stmt->bind_param("s", $roll_number);
                if (!$stmt->execute()) {
                    $error_message = "Database query execution failed.";
                    error_log("Query execution failed: " . $stmt->error);
                } else {
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $admit = $result->fetch_assoc();
                        $pdf_url = $admit['pdf_url'];
                        error_log("Found PDF URL: $pdf_url for roll number: $roll_number");
                        if (filter_var($pdf_url, FILTER_VALIDATE_URL)) {
                            $_SESSION[$attempt_key] = 0;
                            header("Location: $pdf_url", true, 302);
                            $stmt->close();
                            $conn->close();
                            ob_end_clean();
                            exit;
                        } else {
                            $error_message = "Invalid PDF URL. Please contact support.";
                            error_log("Invalid PDF URL: $pdf_url for roll number: $roll_number");
                        }
                    } else {
                        $error_message = "No admit card found for this roll number. Please check and try again.";
                        error_log("No admit card found for roll number: $roll_number");
                    }
                }
                $stmt->close();
            }
        }
    }
}

$sql_contact = "SELECT address, phone_numbers, email, whatsapp_number, office_hours FROM contact_info LIMIT 1";
$result_contact = $conn ? $conn->query($sql_contact) : false;
if (!$result_contact || $result_contact->num_rows === 0) {
    error_log("Error fetching contact info: " . ($conn ? $conn->error : 'No database connection'));
    $contact = [
        'address' => 'Sangrampur, Kalikapota, West Bengal 743355',
        'phone_numbers' => '9091850061, 9163588008',
        'email' => 'childrenscorner85@gmail.com',
        'whatsapp_number' => '9163588008',
        'office_hours' => 'Mon - Sat: 9 AM - 5 PM'
    ];
} else {
    $contact = $result_contact->fetch_assoc();
}

if ($conn) {
    $conn->close();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Download Admit Card - Children's Corner Institution X Ilhaam Mission</title>
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
        .breadcrumb-section {
            background: var(--gradient-accent);
            color: #ffffff;
            padding: 2rem 0;
        }
        .breadcrumb-item a {
            color: #ffffff;
            transition: var(--transition);
        }
        .breadcrumb-item a:hover {
            color: rgba(255, 255, 255, 0.8);
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
        .form-card {
            background: var(--foreground);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            max-width: 500px;
            margin: 0 auto;
        }
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .form-icon {
            color: var(--accent);
        }
        .form-control {
            border-radius: var(--radius);
            border: 2px solid var(--border);
            padding: 0.75rem;
            transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(58, 12, 163, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.9rem;
        }
        .btn-download {
            background: var(--gradient-accent);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: var(--radius);
            color: #ffffff;
            width: 100%;
            transition: var(--transition);
        }
        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }
        .btn-download:disabled {
            background: var(--muted);
            cursor: not-allowed;
        }
        .error-msg {
            color: #dc3545;
            font-weight: 600;
            margin-top: 0.5rem;
            text-align: center;
        }
        .contact-card {
            background: var(--foreground);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-top: 2rem;
            text-align: center;
            transition: var(--transition);
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .contact-card i {
            color: var(--accent);
            margin-right: 0.5rem;
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
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--accent);
            color: #ffffff;
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
            .form-card, .contact-card {
                padding: 1.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="notice" data-animate-hover="tada">Notices</a></li>
                    <li class="nav-item"><a class="nav-link active" href="admission" data-animate-hover="tada">Admission</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery" data-animate-hover="tada">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="donation" data-animate-hover="tada">Donate</a></li>
                    <li class="nav-item"><a class="nav-link" href="success" data-animate-hover="tada">Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="activities" data-animate-hover="tada">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty" data-animate-hover="tada">Staff</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact" data-animate-hover="tada">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Section -->
    <section class="breadcrumb-section animate__animated" data-animate="fadeIn">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item animate__animated" data-animate="bounceIn" data-delay="0.1s"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active animate__animated" data-animate="bounceIn" data-delay="0.3s" aria-current="page">Admit Card</li>
                </ol>
            </nav>
            <h1 class="text-white animate__animated" data-animate="fadeInDown">Download Admit Card</h1>
            <p class="lead text-white mb-0 animate__animated" data-animate="fadeInUp">Enter your roll number to download your admit card</p>
        </div>
    </section>

    <!-- Admit Card Form Section -->
    <section class="section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title text-center animate__animated" data-animate="fadeInDown">
                <h2>Admit Card Download</h2>
                <div class="title-divider"></div>
                <p>Securely download your personalized admit card</p>
            </div>
            <div class="form-card animate__animated" data-animate="fadeInUp">
                <div class="text-center mb-4">
                    <i class="fas fa-id-card fa-3x form-icon mb-3 animate__animated" data-animate="zoomIn"></i>
                    <h4>Enter Roll Number</h4>
                    <p class="text-muted">Format: Letters and numbers only</p>
                </div>
                <form id="admitForm" method="POST" action="admit.php" class="needs-validation" novalidate>
                    <input type="text" id="rollInput" name="roll_number" class="form-control mb-3 animate__animated" data-animate="fadeIn" placeholder="Enter Roll Number" value="<?php echo htmlspecialchars($roll_number); ?>" required>
                    <div class="invalid-feedback">Please enter a valid roll number (letters and numbers only).</div>
                    <button type="submit" class="btn btn-download animate__animated" data-animate="fadeIn" data-animate-hover="pulse" id="downloadButton"><i class="fas fa-download me-2"></i>Download Admit Card</button>
                    <?php if ($error_message): ?>
                        <p class="error-msg animate__animated" data-animate="fadeIn"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>
                </form>
            </div>
            <div class="contact-card animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                <h3>Contact Us for Support</h3>
                <p><i class="fas fa-phone"></i>Phone: <?php echo htmlspecialchars($contact['phone_numbers']); ?></p>
                <p><i class="fas fa-envelope"></i>Email: <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></p>
                <p><i class="fab fa-whatsapp"></i>WhatsApp: <a href="https://wa.me/<?php echo htmlspecialchars(str_replace('+', '', $contact['whatsapp_number'])); ?>" target="_blank"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></a></p>
                <p class="text-muted">For any issues with downloading your admit card, please reach out to us.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer animate__animated" data-animate="fadeInUp">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="animate__animated" data-animate="fadeIn">About Us</h5>
                    <p class="animate__animated" data-animate="fadeInUp" data-delay="0.1s">Children’s Corner Institution & Ilhaam Mission is dedicated to the holistic development of vulnerable and marginalized populations, ensuring success through education, health, and livelihood initiatives.</p>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/assets/js/admit.js"></script>
</body>
</html>
