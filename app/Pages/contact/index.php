<?php
require_once dirname(__DIR__, 3) . '/config.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Log inputs for debugging
    error_log("Contact Form Input: name='$name', email='$email', mobile='$mobile', subject='$subject', message='$message'");

    // Server-side validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if (empty($mobile) || !preg_match('/^[6-9][0-9]{9}$/', preg_replace('/[^0-9]/', '', $mobile))) {
        $errors[] = 'Valid 10-digit mobile number is required';
    }
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    if (!empty($errors)) {
        error_log("Validation Errors: " . implode(', ', $errors));
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, mobile, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to prepare statement']);
        exit;
    }
    $stmt->bind_param("sssss", $name, $email, $mobile, $subject, $message);

    if ($stmt->execute()) {
        error_log("Form submission successful for: $email");
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your message! We will contact you shortly.']);
    } else {
        error_log("Database error: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    exit;
}

// Fetch contact information
$sql_contact = "SELECT address, phone_numbers, email, whatsapp_number, office_hours FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
if (!$result_contact || $result_contact->num_rows === 0) {
    error_log("Error fetching contact information: " . $conn->error);
    die("Error fetching contact information.");
}
$contact = $result_contact->fetch_assoc();

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
    <title>Contact - Ilhaam Mission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Noto+Sans+Bengali:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
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
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
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
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
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
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
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
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            margin: 1rem auto;
            border-radius: var(--radius);
        }
        .contact-form-section {
            background: var(--background);
            padding: 3rem 0;
        }
        .form-card {
            background: var(--foreground);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .form-label {
            font-weight: 600;
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
        .btn-submit {
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: var(--radius);
            color: #ffffff;
            width: 100%;
            transition: var(--transition);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }
        .btn-submit:disabled {
            background: var(--muted);
            cursor: not-allowed;
        }
        .contact-info-card {
            background: var(--foreground);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }
        .contact-info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .contact-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border-radius: var(--radius);
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            color: #ffffff;
            font-size: 1.2rem;
            transition: var(--transition);
        }
        .contact-info-card:hover .contact-icon {
            transform: scale(1.1);
        }
        .contact-title {
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.75rem;
        }
        .contact-detail {
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .contact-link {
            color: var(--accent);
            transition: var(--transition);
        }
        .contact-link:hover {
            color: var(--accent-hover);
        }
        .map-section {
            padding: 3rem 0;
        }
        .map-container {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            height: 400px;
        }
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .footer {
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
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
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 400px;
            width: 100%;
        }
        .notification.success {
            background: linear-gradient(45deg, #28a745, #34c759);
        }
        .notification.error {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
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
            .map-container {
                height: 300px;
            }
        }
        @media (max-width: 768px) {
            .section-title h2 {
                font-size: 2rem;
            }
            .section-title p {
                font-size: 1rem;
            }
            .section {
                padding: 2rem 0;
            }
            .form-card,
            .contact-info-card {
                padding: 1.5rem;
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

    <!-- Notification Container -->
    <div class="notification-container animate__animated" data-animate="fadeIn" id="notificationContainer"></div>

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
                    <li class="nav-item"><a class="nav-link" href="gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="donation">Donate</a></li>
                    <li class="nav-item"><a class="nav-link" href="success">Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link" href="activities">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty">faculty</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact">Contact</a></li>
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
                    <li class="breadcrumb-item active animate__animated" data-animate="bounceIn" data-delay="0.2s" aria-current="page">Contact</li>
                </ol>
            </nav>
            <h1 class="text-white animate__animated" data-animate="fadeInDown">Contact Us</h1>
            <p class="lead text-white mb-0 animate__animated" data-animate="fadeInUp">Get in touch with us for any queries or information</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="section contact-form-section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Get In Touch</h2>
                <div class="title-divider"></div>
                <p>Have questions? We're here to help. Send us a message and we'll respond as soon as possible.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card animate__animated" data-animate="fadeInUp">
                        <form id="contactForm" class="needs-validation" novalidate method="post" action="contact.php">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label animate__animated" data-animate="fadeIn">Full Name *</label>
                                    <input type="text" class="form-control animate__animated" data-animate="fadeIn" data-delay="0.1s" id="name" name="name" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                    <div class="invalid-feedback">Please enter a valid name.</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label animate__animated" data-animate="fadeIn">Email Address *</label>
                                    <input type="email" class="form-control animate__animated" data-animate="fadeIn" data-delay="0.1s" id="email" name="email" placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="mobile" class="form-label animate__animated" data-animate="fadeIn">Mobile Number *</label>
                                    <input type="tel" class="form-control animate__animated" data-animate="fadeIn" data-delay="0.1s" id="mobile" name="mobile" placeholder="Enter your mobile number" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required pattern="[6-9][0-9]{9}">
                                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="subject" class="form-label animate__animated" data-animate="fadeIn">Subject *</label>
                                    <select class="form-control animate__animated" data-animate="fadeIn" data-delay="0.1s" id="subject" name="subject" required>
                                        <option value="">Select a subject</option>
                                        <option value="admission" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'admission') ? 'selected' : ''; ?>>Admission Query</option>
                                        <option value="course" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'course') ? 'selected' : ''; ?>>Course Information</option>
                                        <option value="fee" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'fee') ? 'selected' : ''; ?>>Fee Structure</option>
                                        <option value="other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a subject.</div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label for="message" class="form-label animate__animated" data-animate="fadeIn">Message *</label>
                                    <textarea class="form-control animate__animated" data-animate="fadeIn" data-delay="0.1s" id="message" name="message" rows="5" placeholder="Enter your message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-submit animate__animated" data-animate="fadeInUp" data-animate-hover="pulse" id="submitButton">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Contact Information</h2>
                <div class="title-divider"></div>
                <p>Reach out to us through any of these channels</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.1s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4 class="contact-title">Address</h4>
                        <?php
                        $address_lines = explode(',', $contact['address']);
                        foreach ($address_lines as $index => $line) {
                            echo '<p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="' . (0.2 + $index * 0.1) . 's">' . htmlspecialchars(trim($line)) . '</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4 class="contact-title">Phone</h4>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.3s"><?php echo htmlspecialchars($contact['phone_numbers']); ?></p>
                        <a href="tel:<?php echo htmlspecialchars($contact['phone_numbers']); ?>" class="contact-link animate__animated" data-animate-hover="pulse">Call Now</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.3s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4 class="contact-title">Email</h4>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.4s"><?php echo htmlspecialchars($contact['email']); ?></p>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.5s">admission@ilhaammission.org</p>
                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="contact-link animate__animated" data-animate-hover="pulse">Send Email</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.4s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h4 class="contact-title">WhatsApp</h4>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.5s"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></p>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.6s">Available 10AM - 6PM</p>
                        <a href="https://wa.me/<?php echo htmlspecialchars(str_replace('+', '', $contact['whatsapp_number'])); ?>" class="contact-link animate__animated" data-animate-hover="pulse" target="_blank">Chat on WhatsApp</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.5s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="far fa-clock"></i>
                        </div>
                        <h4 class="contact-title">Office Hours</h4>
                        <?php
                        $office_hours_lines = explode(',', $contact['office_hours']);
                        foreach ($office_hours_lines as $index => $line) {
                            echo '<p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="' . (0.6 + $index * 0.1) . 's">' . htmlspecialchars(trim($line)) . '</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="contact-info-card animate__animated" data-animate="fadeInUp" data-delay="0.6s">
                        <div class="contact-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4 class="contact-title">Social Media</h4>
                        <p class="contact-detail animate__animated" data-animate="fadeInUp" data-delay="0.7s">Follow us for updates</p>
                        <div class="social-icons justify-content-center">
                            <a href="https://facebook.com/ilhaammission" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://instagram.com/ilhaammission" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-instagram"></i></a>
                            <a href="https://twitter.com/ilhaammission" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-twitter"></i></a>
                            <a href="https://youtube.com/ilhaammission" target="_blank" class="animate__animated" data-animate-hover="pulse"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Our Location</h2>
                <div class="title-divider"></div>
                <p>Find us easily with the map below</p>
            </div>
            <div class="map-container animate__animated" data-animate="fadeInUp">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3686.246050436142!2d88.51087961489545!3d22.49334548523172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjLCsDI5JzM2LjAiTiA4OMKwMzAnNDMuNiJF!5e0!3m2!1sen!2sin!4v1697801234567!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

        // Notification Function
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            if (!container) return;
            const notification = document.createElement('div');
            notification.className = `notification ${type} animate__animated animate__slideInRight`;
            notification.style.cssText = `
                padding: 12px 20px;
                margin-bottom: 10px;
                border-radius: 8px;
                color: white;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            `;
            notification.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                ${message}
            `;
            container.appendChild(notification);
            setTimeout(() => notification.remove(), 5000);
        }

        // Form Submission
        document.addEventListener('DOMContentLoaded', () => {
            handleAnimations();

            const form = document.getElementById('contactForm');
            const submitButton = document.getElementById('submitButton');
            if (!form || !submitButton) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    showNotification('Please fill all required fields correctly.', 'error');
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

                const formData = new FormData(form);
                try {
                    const response = await fetch('contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        showNotification(result.message, 'success');
                        form.reset();
                        form.classList.remove('was-validated');
                    } else {
                        showNotification(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Message';
                }
            });
        });
    </script>
</body>
</html>
