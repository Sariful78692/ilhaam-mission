<?php
// index.php
require_once 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch contact info for footer
$sql_contact = "SELECT address, phone_numbers, email, office_hours, whatsapp_number FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
$contact = $result_contact && $result_contact->num_rows > 0 ? $result_contact->fetch_assoc() : [
    'address' => 'Sangrampur, Kalikapota, West Bengal 743355, India',
    'phone_numbers' => '9091850061, 9163588008, 86376446',
    'email' => 'childrenscorner85@gmail.com',
    'office_hours' => 'Monday - Saturday: 8AM - 8PM, Sunday: Closed',
    'whatsapp_number' => '9163588008'
];
if (!$result_contact) {
    error_log("Contact info query error: " . $conn->error);
}

// Fetch banners
$sql_banners = "SELECT url, alt, link FROM banners WHERE status = 'active' ORDER BY created_at DESC";
$result_banners = $conn->query($sql_banners);
$banners = [];
if ($result_banners) {
    while ($row = $result_banners->fetch_assoc()) {
        $banners[] = $row;
    }
} else {
    error_log("Banners query error: " . $conn->error);
}

// Fetch notifications
$sql_notifications = "SELECT title, text, date, link, type, new_duration FROM notifications WHERE status = 'active' ORDER BY date DESC";
$result_notifications = $conn->query($sql_notifications);
$notifications = [];
if ($result_notifications) {
    while ($row = $result_notifications->fetch_assoc()) {
        $notifications[] = $row;
    }
} else {
    error_log("Notifications query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilhaam Mission - Empowering Girls Through Education</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <meta name="description" content="Ilhaam Mission High School offers academic excellence and holistic development for girls in Classes V-XII, fostering moral and religious values.">
    <meta name="keywords" content="girls education, Ilhaam Mission, high school, Classes V-XII, moral education">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #2575fc;
            --success: #4cc9f0;
            --warning: #f9c74f;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --text: #2d3748;
            --text-light: #718096;
            --gradient-primary: linear-gradient(135deg, #4361ee, #3a0ca3);
            --gradient-accent: linear-gradient(135deg, #6a11cb, #2575fc);
            --shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            --shadow-light: 0 4px 10px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease-in-out;
            --font-family: 'Montserrat', 'Poppins', 'Segoe UI', Arial, sans-serif;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font-family);
            color: var(--text);
            background-color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
            scroll-behavior: smooth;
            min-height: 100vh;
        }
        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }
        .section, .hero, .banner-section, .footer {
            visibility: visible !important;
            opacity: 1 !important;
            min-height: 100px;
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
            margin: 0 8px;
            position: relative;
            font-size: clamp(0.9rem, 3vw, 1rem);
            padding: 8px 12px;
        }
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: var(--gradient-accent);
            transition: var(--transition);
            border-radius: 10px;
        }
        .nav-link:hover:after, .nav-link.active:after {
            width: 100%;
        }
        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }
        .banner-section {
            height: 50vh;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0;
        }
        .banner-carousel {
            width: 100%;
            height: 100%;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
        }
        .carousel-inner, .carousel-item {
            height: 100%;
        }
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            cursor: pointer;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            transition: var(--transition);
        }
        .carousel-control-prev {
            left: 10px;
        }
        .carousel-control-next {
            right: 10px;
        }
        .carousel-control-prev:hover, .carousel-control-next:hover {
            background: var(--accent);
            opacity: 1;
        }
        .carousel-indicators {
            bottom: 15px;
        }
        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
            background-color: #fff;
            border: 2px solid var(--primary);
            transition: var(--transition);
        }
        .carousel-indicators .active {
            background-color: var(--accent);
            transform: scale(1.2);
        }
        .hero {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.85)), 
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: var(--gradient-primary);
            opacity: 0.05;
            transform: rotate(-15deg);
            z-index: -1;
        }
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        .hero h1 {
            font-size: clamp(2rem, 6vw, 3rem);
            font-weight: 900;
            margin-bottom: 20px;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }
        .hero .lead {
            font-size: clamp(1rem, 3.5vw, 1.4rem);
            margin-bottom: 30px;
            color: var(--text);
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            font-size: clamp(0.85rem, 2.5vw, 0.95rem);
            border-radius: 50px;
            transition: var(--transition);
            box-shadow: var(--shadow);
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            min-height: 44px;
            touch-action: manipulation;
        }
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-accent);
            transition: var(--transition);
            z-index: -1;
        }
        .btn-primary:hover:before {
            left: 0;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            color: #fff;
        }
        .btn-outline-primary {
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 8px 20px;
            font-weight: 600;
            font-size: clamp(0.85rem, 2.5vw, 0.95rem);
            border-radius: 50px;
            transition: var(--transition);
            background: transparent;
            min-height: 44px;
            touch-action: manipulation;
        }
        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: transparent;
        }
        .section {
            padding: 50px 0;
            position: relative;
        }
        .section-title {
            margin-bottom: 30px;
            text-align: center;
        }
        .section-title h2 {
            font-size: clamp(1.6rem, 5vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 10px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .section-title p {
            color: var(--text-light);
            font-size: clamp(0.85rem, 3vw, 1rem);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
        }
        .title-divider {
            height: 3px;
            width: 80px;
            background: var(--gradient-accent);
            margin: 10px auto;
            border-radius: 10px;
        }
        .feature-box {
            text-align: center;
            padding: 20px 15px;
            border-radius: 10px;
            background: white;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            background: var(--gradient-primary);
            color: white;
        }
        .feature-box:hover h3, .feature-box:hover p {
            color: white;
        }
        .feature-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border-radius: 10px;
            background: var(--gradient-primary);
            color: white;
            font-size: 24px;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }
        .feature-box:hover .feature-icon {
            background: var(--gradient-accent);
            transform: scale(1.1);
        }
        .feature-box h3 {
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 600;
            font-size: clamp(1.1rem, 3vw, 1.2rem);
        }
        .feature-box p {
            color: var(--text-light);
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            margin-bottom: 15px;
            height: 100%;
            overflow: hidden;
            background: white;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        .card-title {
            color: var(--primary);
            font-weight: 600;
            font-size: clamp(1.1rem, 3vw, 1.2rem);
            margin-bottom: 10px;
        }
        .card-body {
            padding: 20px !important;
        }
        .form-control {
            padding: 10px 12px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: var(--transition);
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            background: #f8f9fa;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.25);
            background: white;
        }
        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.7;
        }
        .footer {
            background: var(--gradient-primary);
            color: white;
            padding: 50px 0 20px;
            position: relative;
        }
        .footer:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--gradient-accent);
        }
        .footer a {
            color: rgba(255, 255, 255, 0.9);
            transition: var(--transition);
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }
        .footer a:hover {
            color: white;
            transform: translateX(5px);
        }
        .footer h5 {
            color: white;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: clamp(1rem, 3vw, 1.2rem);
            position: relative;
            padding-bottom: 6px;
        }
        .footer h5:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: var(--accent);
            border-radius: 10px;
        }
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            margin-right: 8px;
            transition: var(--transition);
        }
        .social-icons a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }
        .animate-up {
            transform: translateY(20px);
            opacity: 0;
            transition: var(--transition);
        }
        .animate-up.animated {
            transform: translateY(0);
            opacity: 1;
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
            background: var(--gradient-warning);
            opacity: 0.08;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: morphing 12s infinite linear;
        }
        .shape-2 {
            top: 50%;
            right: 2%;
            width: 120px;
            height: 120px;
            background: var(--gradient-success);
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
        .notification-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.98));
            border-radius: 8px;
            padding: 15px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            background: var(--gradient-accent);
            color: white;
        }
        .notification-card:hover h4,
        .notification-card:hover p,
        .notification-card:hover .notification-date,
        .notification-card:hover .notification-link {
            color: white;
        }
        .notification-card h4 {
            font-size: clamp(1rem, 2.5vw, 1.1rem);
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .notification-card p {
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            color: var(--text-light);
            margin-bottom: 6px;
        }
        .notification-date {
            font-size: clamp(0.75rem, 2vw, 0.85rem);
            color: var(--text-light);
            font-style: italic;
        }
        .notification-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--gradient-accent);
            transition: var(--transition);
        }
        .notification-card:hover::before {
            width: 6px;
        }
        .notification-link {
            display: inline-flex;
            align-items: center;
            background: var(--gradient-primary);
            color: white;
            padding: 6px 15px;
            border-radius: 50px;
            font-size: clamp(0.75rem, 2vw, 0.85rem);
            font-weight: 600;
            margin-top: 8px;
            transition: var(--transition);
            min-height: 44px;
            touch-action: manipulation;
        }
        .notification-link:hover {
            background: var(--gradient-accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            color: white;
        }
        .notification-link i {
            margin-right: 6px;
            font-size: 14px;
        }
        .new-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--gradient-warning);
            color: white;
            font-size: clamp(0.7rem, 2vw, 0.8rem);
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .academy-logo {
            width: 96px;
            height: 96px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: var(--light);
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
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
        @media (max-width: 992px) {
            .hero, .section {
                padding: 40px 0;
            }
            .hero h1, .section-title h2 {
                font-size: clamp(1.6rem, 5vw, 2rem);
            }
            .hero .lead {
                font-size: clamp(0.9rem, 3vw, 1.1rem);
            }
            .banner-section {
                height: 40vh;
            }
            .carousel-control-prev, .carousel-control-next {
                width: 35px;
                height: 35px;
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
            .hero, .section {
                padding: 30px 0;
            }
            .hero h1, .section-title h2 {
                font-size: clamp(1.4rem, 4.5vw, 1.8rem);
            }
            .hero .lead {
                font-size: clamp(0.85rem, 2.8vw, 1rem);
            }
            .navbar-brand {
                font-size: clamp(1.2rem, 3.5vw, 1.4rem);
            }
            .nav-link {
                margin: 5px 0;
                font-size: clamp(0.85rem, 2.8vw, 0.95rem);
            }
            .banner-section {
                height: 35vh;
            }
            .carousel-control-prev, .carousel-control-next {
                display: none;
            }
            .feature-box, .card-body {
                padding: 15px;
            }
            .notification-card {
                padding: 12px;
            }
        }
        @media (max-width: 576px) {
            .hero h1, .section-title h2 {
                font-size: clamp(1.3rem, 4vw, 1.5rem);
            }
            .hero .lead {
                font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            }
            .section-title p {
                font-size: clamp(0.75rem, 2.5vw, 0.85rem);
            }
            .form-control {
                padding: 8px 10px;
                font-size: clamp(0.75rem, 2vw, 0.85rem);
            }
            .btn-primary, .btn-outline-primary {
                padding: 8px 15px;
                font-size: clamp(0.75rem, 2vw, 0.85rem);
                min-height: 36px;
            }
            .banner-section {
                height: 30vh;
            }
            .carousel-indicators {
                bottom: 8px;
            }
            .notification-card {
                padding: 10px;
            }
            .notification-card h4 {
                font-size: clamp(0.9rem, 2.2vw, 1rem);
            }
            .notification-card p {
                font-size: clamp(0.75rem, 1.8vw, 0.85rem);
            }
            .notification-link {
                padding: 5px 12px;
                font-size: clamp(0.7rem, 1.8vw, 0.8rem);
                min-height: 36px;
            }
            .shape {
                display: none;
            }
            .academy-logo {
                width: 80px;
                height: 80px;
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
                        <span class="logo-tagline">Empowering Girls Through Education</span>
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
                    <li class="nav-item"><a class="nav-link" href="contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner Section -->
    <?php if (!empty($banners)) { ?>
    <section class="banner-section">
        <div class="container-fluid p-0">
            <div id="bannerCarousel" class="carousel slide banner-carousel carousel-fade" data-bs-ride="carousel" data-bs-touch="true" data-bs-interval="4000">
                <div class="carousel-indicators" id="carouselIndicators">
                    <?php
                    $validBanners = array_filter($banners, function($banner) {
                        return !empty($banner['link']) && $banner['link'] !== 'index.php' && $banner['link'] !== '#';
                    });
                    foreach ($validBanners as $index => $banner) {
                        $activeClass = $index === 0 ? 'class="active" aria-current="true"' : '';
                        echo "<button type=\"button\" data-bs-target=\"#bannerCarousel\" data-bs-slide-to=\"$index\" $activeClass aria-label=\"Slide " . ($index + 1) . "\"></button>";
                    }
                    ?>
                </div>
                <div class="carousel-inner" id="carouselInner">
                    <?php
                    foreach ($validBanners as $index => $banner) {
                        $activeClass = $index === 0 ? 'carousel-item active' : 'carousel-item';
                        $bannerLink = htmlspecialchars($banner['link']);
                        echo "<div class=\"$activeClass\">";
                        echo "<a href=\"$bannerLink\" class=\"banner-link\" data-link=\"$bannerLink\">";
                        echo "<img src=\"" . htmlspecialchars($banner['url']) . "\" class=\"d-block w-100\" alt=\"" . htmlspecialchars($banner['alt']) . "\" loading=\"lazy\">";
                        echo "</a>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>
    <?php } else { ?>
    <script>console.log('No valid banners found in database.');</script>
    <?php } ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="animate__animated animate__fadeInDown">ILHAAM MISSION</h1>
                <p class="lead animate__animated animate__fadeInUp">Empowering Girls Through Academic Excellence and Moral Values</p>
                <div class="animate__animated animate__zoomIn">
                    <a href="admission.php" class="btn btn-primary btn-lg me-3">Admission Enquiry</a>
                    <a href="about.php" class="btn btn-outline-primary btn-lg">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Facilities Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Our Facilities</h2>
                <div class="title-divider"></div>
                <p>Supportive environment fostering academic and personal growth for girls</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3>Girls Hostel</h3>
                        <p>Safe and comfortable accommodation for girls, promoting a focused study environment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-pray"></i>
                        </div>
                        <h3>Wazu, Namaz & Iftaar Facility</h3>
                        <p>Dedicated spaces for religious practices, nurturing spiritual growth.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Friendly & Religious Environment</h3>
                        <p>A supportive and value-driven atmosphere to foster discipline and moral growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifications Section -->
    <?php if (!empty($notifications)) { ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Latest Notifications</h2>
                <div class="title-divider"></div>
                <p>Stay updated with the latest announcements and events at Ilhaam Mission</p>
            </div>
            <div class="row g-3">
                <?php
                $currentDate = new DateTime();
                foreach ($notifications as $notification) {
                    $notificationDate = new DateTime($notification['date']);
                    $interval = $currentDate->diff($notificationDate);
                    $newDuration = isset($notification['new_duration']) ? (int)$notification['new_duration'] : 7;
                    $isNew = $interval->days <= $newDuration;
                ?>
                <div class="col-md-4 col-12">
                    <div class="notification-card animate-up">
                        <?php if ($isNew) { ?>
                        <span class="new-badge">New</span>
                        <?php } ?>
                        <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                        <p><?php echo htmlspecialchars($notification['text']); ?></p>
                        <?php if (!empty($notification['date'])) { ?>
                        <div class="notification-date">Posted on: <?php echo htmlspecialchars($notification['date']); ?></div>
                        <?php } ?>
                        <?php if (!empty($notification['link'])) { ?>
                        <a href="<?php echo htmlspecialchars($notification['link']); ?>" 
                           class="notification-link" 
                           <?php echo $notification['type'] === 'pdf' ? 'download' : ''; ?>
                           onclick="<?php echo $notification['type'] === 'pdf' ? 'return confirmDownload(this);' : ''; ?>">
                            <i class="fas <?php echo $notification['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-link'; ?>"></i>
                            <?php echo $notification['type'] === 'pdf' ? 'Download PDF' : 'View Details'; ?>
                        </a>
                        <?php } else { ?>
                        <span class="notification-link disabled" style="background: #ccc; cursor: not-allowed;">File Not Available</span>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php } else { ?>
    <script>console.log('No active notifications found in database.');</script>
    <?php } ?>

    <!-- Why Choose Us Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Us?</h2>
                <div class="title-divider"></div>
                <p>Discover what sets Ilhaam Mission apart in nurturing future leaders</p>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3>Holistic Development</h3>
                        <p>Balancing academic excellence with moral and religious values.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-female"></i>
                        </div>
                        <h3>Girls-Centric Education</h3>
                        <p>Tailored programs for girls in Classes V-XII, fostering empowerment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-pray"></i>
                        </div>
                        <h3>Religious Guidance</h3>
                        <p>Support for spiritual growth through dedicated facilities and counseling.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3>Safe Accommodation</h3>
                        <p>Girls hostel ensuring a secure and focused study environment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Expert Faculty</h3>
                        <p>Experienced educators providing personalized academic guidance.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box animate-up">
                        <div class="feature-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>Comprehensive Curriculum</h3>
                        <p>Well-rounded education preparing students for higher studies and life.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>About Us</h2>
                <div class="title-divider"></div>
                <p>Discover Ilhaam Mission High School - where academic excellence meets holistic student development</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <p class="lead animate__animated animate__fadeInUp">Admission for girls in Classes V-XII, fostering academic and moral growth.</p>
                    <p class="animate__animated animate__fadeInUp">In 1985, the esteemed educational institution established by Jaharul Haque Haldar Mahashay began its journey as the first nursery and KG school in the area. Currently, the institution is run by the Ilhaam Mission Trust, offering education alongside moral and ethical guidance. In addition to primary education, the institution also runs two sections — residential and non-residential — for social and moral development. Various educational initiatives have been launched to nurture and elevate students’ educational standards through residential programs, ensuring they grow in a beautiful environment.</p>
                    <p class="animate__animated animate__fadeInUp">From the 2025 academic year, Ilhaam Mission has introduced residential (8:00 AM – 8:00 PM) and non-residential (10:40 AM – 3:45 PM) education systems for talented female students.</p>
                    <h3 class="animate__animated animate__fadeInUp">Goals and Objectives</h3>
                    <p class="animate__animated animate__fadeInUp">As time progresses, religious practices are fading. If we cannot nurture our daughters with proper religious teachings at home, the nation’s future will be at risk. The spread of misguided societal influences can divert our daughters from the right path. To keep our girls safe and nurture them into capable individuals, their education should not be limited to literacy alone. Without balancing moral values, their education will not serve society properly, and the nation will fall behind.</p>
                    <p class="animate__animated animate__fadeInUp">In many families, having a separate study room for girls is difficult, making focused learning a challenge. Additionally, the widespread use of TV, mobile phones, and social media at home distracts students. As a result, girls are becoming increasingly undisciplined. Proper guidance and counseling can mold students into extraordinary individuals.</p>
                    <p class="animate__animated animate__fadeInUp">Today, women are excelling not only in the medical field but also in other professions. However, if a woman wants to become a nurse or doctor, she must first receive education. Our dream is to see our daughters achieve such goals while preserving their moral and religious values. Mothers play the most crucial role in fulfilling these dreams by nurturing their children properly.</p>
                    <p class="animate__animated animate__fadeInUp">With this perspective, the institution has expanded its primary section to a secondary level. Under Ilhaam Mission, female students receive comprehensive moral and educational training. The goal is to prepare these students to become competent individuals and guide them towards higher education (NEET) in the future. We aim to shape them into future doctors and ensure they develop into exceptional individuals.</p>
                    <div class="mt-4 animate__animated animate__zoomIn">
                        <a href="about.php" class="btn btn-primary me-3">Learn More</a>
                        <a href="contact.php" class="btn btn-outline-primary">Visit Campus</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Contact Us</h2>
                <div class="title-divider"></div>
                <p>Get in touch with us for more information or to schedule a visit</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow animate-up">
                        <div class="card-body p-4">
                            <form id="contactForm" class="row g-3" action="submit_contact.php" method="POST">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email" placeholder="Email ID" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" name="mobile" placeholder="Mobile Number" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" name="message" rows="4" placeholder="Your Message" required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>Ilhaam Mission High School</h5>
                    <p class="mt-3">Empowering Girls Through Education</p>
                    <p>We are committed to providing holistic education for girls, balancing academic excellence with moral and religious values.</p>
                    <div class="social-icons">
                        <a href="https://facebook.com/ilhaammission"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com/ilhaammission"><i class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com/ilhaammission"><i class="fab fa-twitter"></i></a>
                        <a href="https://youtube.com/ilhaammission"><i class="fab fa-youtube"></i></a>
                        <a href="https://linkedin.com/company/ilhaammission"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($contact['address']); ?></li>
                        <li class="mb-3"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($contact['phone_numbers']); ?></li>
                        <li class="mb-3"><i class="fas fa-envelope me-2"></i><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></li>
                        <li class="mb-3"><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($contact['office_hours']); ?></li>
                        <li class="mb-3"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/<?php echo htmlspecialchars($contact['whatsapp_number']); ?>" target="_blank"><?php echo htmlspecialchars($contact['whatsapp_number']); ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3"><a href="index.php"><i class="fas fa-angle-right me-2"></i>Home</a></li>
                        <li class="mb-3"><a href="about.php"><i class="fas fa-angle-right me-2"></i>About Us</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row pt-3">
                <div class="col-md-12 text-center">
                    <p>&copy; 2025 Children's Corner Institution & Ilhaam Mission. All Rights Reserved.</p>
                </div>
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
        function confirmDownload(link) {
            if (link.getAttribute('href') === '') {
                alert('PDF file is not available. Please contact support.');
                return false;
            }
            return true;
        }

        // Animation Handler
        function handleAnimations() {
            const elements = document.querySelectorAll('.animate-up');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            elements.forEach(element => observer.observe(element));
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleAnimations();
        });
    </script>

</body>
</html>
<?php
$conn->close();
?>