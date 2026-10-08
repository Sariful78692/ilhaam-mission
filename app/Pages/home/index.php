<?php
// index.php
require_once dirname(__DIR__, 3) . '/config.php';

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
            /* Professional site header and footer */
        .navbar.professional-site-chrome, .navbar {
            background: rgba(255,255,255,.98);
            box-shadow: 0 4px 24px rgba(24,39,75,.09);
            border-bottom: 1px solid #e9edf5;
            padding: .7rem 0;
            backdrop-filter: blur(14px);
        }
        .navbar .container { gap: 1rem; }
        .navbar-brand { padding: .2rem 0; }
        .navbar-brand .logo-container { display:flex; align-items:center; gap:.8rem; }
        .navbar-brand .logo-text { margin-left:0; }
        .navbar-brand .logo-title { font-size:1.18rem; letter-spacing:.035em; }
        .navbar-brand .logo-tagline { font-size:.72rem; color:#64748b; letter-spacing:.015em; }
        .navbar .nav-link {
            color:#334155 !important; font-size:.84rem; font-weight:600;
            margin:0 1px; padding:.62rem .58rem; border-radius:8px;
        }
        .navbar .nav-link::after { height:0; }
        .navbar .nav-link:hover, .navbar .nav-link:focus {
            color:var(--primary) !important; background:#f1f5ff; transform:none;
        }
        .navbar-toggler { border:1px solid #dbe2ef; padding:.4rem .6rem; border-radius:9px; }
        .academy-logo { width:58px; height:58px; object-fit:contain; }
        .footer {
            background:#101d36; color:#d6deeb; padding:30px 0 12px;
            border-top:4px solid #4361ee;
        }
        .footer:before { display:none; }
        .footer h5 { color:#fff; font-size:.95rem; font-weight:700; margin-bottom:.65rem; padding-bottom:.45rem; }
        .footer h5:after { width:38px; height:3px; background:#7c9cff; }
        .footer p { color:#b7c2d5; font-size:.85rem; line-height:1.6; margin-bottom:.5rem; }
        .footer ul li { color:#c7d0df; font-size:.84rem; line-height:1.5; overflow-wrap:anywhere; }
        .footer a { color:#d6deeb; font-size:.88rem; }
        .footer a:hover { color:#fff; transform:translateX(3px); }
        .footer .social-icons { display:flex; gap:.5rem; margin-top:.75rem; }
        .footer .social-icons a { width:38px; height:38px; margin:0; color:#fff; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.13); }
        .footer .social-icons a:hover { background:#4361ee; transform:translateY(-2px); }
        .footer hr { border-color:rgba(255,255,255,.16); opacity:1; margin-top:1rem !important; margin-bottom:.5rem !important; }
        .footer .text-center p { margin:0; color:#9eabc0; font-size:.8rem; }
        @media (max-width: 991.98px) {
            .navbar-collapse { padding:.7rem 0 .25rem; }
            .navbar .navbar-nav { align-items:stretch; padding-top:.4rem; }
            .navbar .nav-link { margin:2px 0; padding:.65rem .8rem; }
        }
        @media (max-width: 575.98px) {
            .academy-logo { width:48px; height:48px; }
            .navbar-brand .logo-title { font-size:1rem; }
            .navbar-brand .logo-tagline { font-size:.64rem; }
            .footer { padding:24px 0 10px; }
        }
            /* site-motion-system */
        @keyframes siteFadeUp {
            from { opacity:0; transform:translateY(14px); }
            to { opacity:1; transform:translateY(0); }
        }
        @keyframes siteFadeIn {
            from { opacity:0; }
            to { opacity:1; }
        }
        body { animation:siteFadeIn .45s ease-out both; }
        .navbar { animation:siteFadeUp .55s ease-out both; }
        .hero, .section, main > section, .banner-section {
            animation:siteFadeUp .65s ease-out both;
        }
        .card, .feature-box, .notification-card, .gallery-item, .faculty-card,
        .activity-card, .notice-card, .contact-card, .admission-card {
            transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }
        .card:hover, .feature-box:hover, .notification-card:hover, .gallery-item:hover,
        .faculty-card:hover, .activity-card:hover, .notice-card:hover, .contact-card:hover,
        .admission-card:hover {
            transform:translateY(-4px);
            box-shadow:0 12px 28px rgba(24,39,75,.13);
        }
        a, button, .btn, .form-control, .form-select, .social-icons a, .nav-link {
            transition:color .2s ease, background-color .2s ease, border-color .2s ease,
                box-shadow .2s ease, transform .2s ease;
        }
        .footer { padding:22px 0 8px; animation:none; }
        .footer .container > .row:first-child > div {
            animation:siteFadeUp .65s ease-out both;
        }
        .footer .container > .row:first-child > div:nth-child(2) { animation-delay:.1s; }
        .footer .container > .row:first-child > div:nth-child(3) { animation-delay:.2s; }
        .footer .container > .row:first-child > div:nth-child(4) { animation-delay:.3s; }
        .footer .mb-4 { margin-bottom:.65rem !important; }
        .footer .mb-3 { margin-bottom:.4rem !important; }
        .footer h5 { margin-bottom:.5rem; padding-bottom:.4rem; }
        .footer .social-icons { margin-top:.55rem; }
        .footer hr { margin-top:.65rem !important; margin-bottom:.35rem !important; }
        .footer .row.pt-3 { padding-top:.4rem !important; }
        .footer .text-center p { animation:siteFadeUp .6s ease-out .25s both; }
        .card, .feature-box, .notification-card, .gallery-item, .faculty-card,
        .activity-card, .notice-card, .contact-card, .admission-card {
            transition:transform .3s ease, box-shadow .3s ease, border-color .3s ease;
            will-change:transform;
        }
        .card:hover, .feature-box:hover, .notification-card:hover, .gallery-item:hover,
        .faculty-card:hover, .activity-card:hover, .notice-card:hover, .contact-card:hover,
        .admission-card:hover {
            transform:translateY(-5px);
            box-shadow:0 14px 30px rgba(24,39,75,.16);
        }
        .banner-section { height:60vh; min-height:380px; }
        @media (max-width: 992px) { .banner-section { height:50vh; min-height:320px; } }
        @media (max-width: 576px) {
            .footer { padding:18px 0 7px; }
            .banner-section { height:42vh; min-height:260px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration:.01ms !important;
                animation-iteration-count:1 !important;
                scroll-behavior:auto !important;
                transition-duration:.01ms !important;
            }
        }
            /* site-all-text-motion */
        @keyframes siteTextReveal {
            from { opacity:0; transform:translateY(8px); }
            to { opacity:1; transform:translateY(0); }
        }
        body :is(h1,h2,h3,h4,h5,h6,p,a,li,span,label,small,strong,em,blockquote,figcaption,legend,dt,dd,th,td) {
            animation:siteTextReveal .65s ease-out both;
            transition:color .25s ease, opacity .25s ease, text-shadow .25s ease;
        }
        body :is(h1,h2,h3,h4,h5,h6) { animation-delay:.05s; }
        body :is(p,li,blockquote,figcaption) { animation-delay:.1s; }
        body :is(a,label,small,strong,em,legend,dt,dd,th,td) { animation-delay:.14s; }
        a:hover, button:hover, .btn:hover { text-shadow:0 1px 10px rgba(67,97,238,.18); }
        @media (prefers-reduced-motion: reduce) {
            body :is(h1,h2,h3,h4,h5,h6,p,a,li,span,label,small,strong,em,blockquote,figcaption,legend,dt,dd,th,td) {
                animation:none !important;
                transition:none !important;
            }
        }
            /* site-footer-text-effects */
        @keyframes footerTextIn {
            from { opacity:0; transform:translateY(9px); }
            to { opacity:1; transform:translateY(0); }
        }
        .footer h5, .footer p, .footer li, .footer a, .footer .text-center p {
            animation:footerTextIn .65s ease-out both;
            transition:color .25s ease, opacity .25s ease, transform .25s ease, text-shadow .25s ease;
        }
        .footer h5 { animation-delay:.05s; }
        .footer p, .footer li { animation-delay:.12s; }
        .footer li:nth-child(2), .footer p:nth-child(2) { animation-delay:.18s; }
        .footer li:nth-child(3), .footer p:nth-child(3) { animation-delay:.24s; }
        .footer li:nth-child(4), .footer p:nth-child(4) { animation-delay:.3s; }
        .footer li:nth-child(5), .footer p:nth-child(5) { animation-delay:.36s; }
        .footer a { display:inline-block; }
        .footer a:hover { color:#fff; transform:translateX(3px); text-shadow:0 2px 12px rgba(255,255,255,.2); }
        .footer .social-icons a:hover { transform:translateY(-3px); }
        @media (prefers-reduced-motion: reduce) {
            .footer h5, .footer p, .footer li, .footer a, .footer .text-center p {
                animation:none !important;
                transition:none !important;
            }
        }
            /* site-footer-social-alignment */
        .footer .social-icons { display:flex; align-items:center; justify-content:flex-start; gap:8px; flex-wrap:wrap; }
        .footer .social-icons a {
            display:inline-flex;
            flex:0 0 38px;
            width:38px;
            height:38px;
            padding:0;
            margin:0;
            align-items:center;
            justify-content:center;
            line-height:1;
            text-align:center;
            transform:none;
        }
        .footer .social-icons a i { display:block; line-height:1; margin:0; }
        .footer .social-icons a:hover { transform:translateY(-3px); }
        @media (max-width: 575.98px) {
            .footer .social-icons { gap:7px; }
            .footer .social-icons a { flex-basis:36px; width:36px; height:36px; }
        }
            /* Distinct home page Goals and Objectives heading */
        .home-goals-heading {
            display:flex; align-items:center; gap:14px;
            margin:28px 0 18px; padding:15px 18px;
            border-left:4px solid #4361ee; border-radius:0 12px 12px 0;
            background:linear-gradient(100deg,#eef2ff 0%,#f8faff 65%,#fff 100%);
            box-shadow:0 5px 18px rgba(40,65,125,.08);
            transition:transform .25s ease, box-shadow .25s ease;
        }
        .home-goals-heading:hover { transform:translateX(4px); box-shadow:0 9px 24px rgba(40,65,125,.13); }
        .home-goals-heading h3 {
            margin:0; color:#263f91; font-size:clamp(1.35rem,3vw,1.7rem);
            font-weight:750; letter-spacing:.01em;
        }
        .home-goals-icon {
            display:inline-flex; align-items:center; justify-content:center;
            flex:0 0 42px; width:42px; height:42px; border-radius:12px;
            background:linear-gradient(135deg,#4361ee,#3a0ca3); color:#fff;
            box-shadow:0 5px 12px rgba(67,97,238,.25);
        }
        .home-goals-icon i { font-size:1.05rem; }
        @media (max-width:575.98px) {
            .home-goals-heading { gap:11px; margin:22px 0 14px; padding:12px; }
            .home-goals-icon { flex-basis:36px; width:36px; height:36px; border-radius:10px; }
        }
            /* site-larger-nav-labels */
        .navbar .nav-link { font-size:.94rem; padding:.68rem .62rem; }
        @media (max-width:991.98px) {
            .navbar .nav-link { font-size:1rem; padding:.72rem .85rem; }
        }
            /* site-colorful-navbar */
        .navbar {
            background:linear-gradient(105deg,#f1f4ff 0%,#fff7fb 52%,#eef8ff 100%);
            border-bottom:1px solid rgba(100,116,220,.2);
            box-shadow:0 5px 22px rgba(57,67,135,.12);
        }
        .navbar .nav-link { border-radius:9px; }
        .navbar .nav-link:hover, .navbar .nav-link:focus, .navbar .nav-link.active {
            color:#4c35c7 !important;
            background:linear-gradient(120deg,rgba(99,102,241,.12),rgba(236,72,153,.09));
        }
        .navbar .nav-link:hover { box-shadow:inset 0 -2px 0 rgba(79,70,229,.55); }
            /* site-colorful-home-hero */
        .hero {
            background-image:
                radial-gradient(ellipse at 12% 15%,rgba(255,196,220,.42),transparent 38%),
                radial-gradient(ellipse at 88% 82%,rgba(130,176,255,.32),transparent 42%),
                linear-gradient(120deg,rgba(238,242,255,.94),rgba(250,240,255,.91) 52%,rgba(230,246,255,.94)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
            background-size:cover;
            background-position:center;
        }
        .hero .lead { color:#33416b; }
            /* home-card-textures */
        .feature-box, .card, .notification-card {
            background-color:#fff;
            background-image:
                radial-gradient(circle at 100% 0,rgba(132,153,255,.16),transparent 38%),
                radial-gradient(circle at 0 100%,rgba(255,190,218,.13),transparent 40%),
                repeating-linear-gradient(135deg,rgba(71,91,160,.025) 0,rgba(71,91,160,.025) 1px,transparent 1px,transparent 9px),
                linear-gradient(145deg,#fff 0%,#f8faff 100%);
            border:1px solid rgba(93,112,190,.12);
            box-shadow:0 8px 22px rgba(44,61,118,.08);
            transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease,background-color .3s ease;
        }
        .feature-box { border-top:3px solid rgba(99,102,241,.55); }
        .feature-box:nth-child(3n+2) { border-top-color:rgba(236,72,153,.52); }
        .feature-box:nth-child(3n) { border-top-color:rgba(14,165,233,.52); }
        .feature-box:hover, .card:hover, .notification-card:hover {
            border-color:rgba(99,102,241,.3);
            box-shadow:0 16px 32px rgba(44,61,118,.16);
        }
        .feature-box .feature-icon { box-shadow:0 7px 16px rgba(67,97,238,.25); }
            /* home-about-left-motion */
        @keyframes homeAboutFromLeft {
            from { opacity:0; transform:translateX(-18px); }
            to { opacity:1; transform:translateX(0); }
        }
        .home-about-section .section-title,
        .home-about-section .col-lg-12 > p,
        .home-about-section .col-lg-12 > h3,
        .home-about-section .col-lg-12 > div {
            animation:homeAboutFromLeft .72s cubic-bezier(.2,.65,.3,1) both;
        }
        .home-about-section .section-title { animation-delay:.04s; }
        .home-about-section .col-lg-12 > p:nth-of-type(1) { animation-delay:.12s; }
        .home-about-section .col-lg-12 > p:nth-of-type(2) { animation-delay:.2s; }
        .home-about-section .col-lg-12 > p:nth-of-type(3) { animation-delay:.28s; }
        .home-about-section .col-lg-12 > h3 { animation-delay:.34s; }
        .home-about-section .col-lg-12 > p:nth-of-type(4) { animation-delay:.4s; }
        .home-about-section .col-lg-12 > p:nth-of-type(5) { animation-delay:.48s; }
        .home-about-section .col-lg-12 > p:nth-of-type(6) { animation-delay:.56s; }
        .home-about-section .col-lg-12 > p:nth-of-type(7) { animation-delay:.64s; }
        .home-about-section .home-about-lead {
            padding:15px 18px; border-left:4px solid #4f46e5; border-radius:0 12px 12px 0;
            background:linear-gradient(100deg,#eef2ff 0%,#fff1f7 58%,#f0f9ff 100%);
            color:#34427a; font-weight:600; box-shadow:0 6px 20px rgba(61,78,145,.09);
        }
        @media (prefers-reduced-motion: reduce) {
            .home-about-section .section-title, .home-about-section .col-lg-12 > p,
            .home-about-section .col-lg-12 > h3, .home-about-section .col-lg-12 > div {
                animation:none !important;
            }
        }
            .footer .quick-links-grid {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            column-gap:.75rem;
            align-items:start;
        }
        .footer .quick-links-grid li { margin-bottom:.4rem !important; }
        @media (max-width:575.98px) {
            .footer .quick-links-grid { column-gap:.4rem; }
        }
            /* site-contact-topbar */
        .site-topbar {
            position:relative;
            z-index:1031;
            background:linear-gradient(100deg,#087eae,#0788bd 55%,#0877a9);
            color:#fff;
            font-size:.84rem;
            line-height:1.3;
        }
        .site-topbar > .container {
            min-height:38px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
        }
        .site-topbar-info, .site-topbar-social { display:flex; align-items:center; }
        .site-topbar-info { gap:clamp(1rem,2.5vw,2rem); min-width:0; }
        .site-topbar-info a, .site-topbar-address {
            display:inline-flex; align-items:center; gap:.55rem; color:#fff;
            white-space:nowrap; transition:opacity .2s ease;
        }
        .site-topbar-info a:hover, .site-topbar-social a:hover { color:#e3f3ff; opacity:.82; }
        .site-topbar-info i { font-size:.82rem; }
        .site-topbar-social { gap:1rem; flex:0 0 auto; }
        .site-topbar-social a { color:#fff; transition:color .2s ease,transform .2s ease; }
        .site-topbar-social a:hover { transform:translateY(-2px); }
        @media (max-width:991.98px) {
            .site-topbar-address { display:none; }
            .site-topbar-info { gap:1rem; }
        }
        @media (max-width:575.98px) {
            .site-topbar { font-size:.73rem; }
            .site-topbar > .container { min-height:34px; gap:.5rem; }
            .site-topbar-info { gap:.75rem; }
            .site-topbar-info a { gap:.35rem; }
            .site-topbar-info a:nth-child(2) span { display:none; }
            .site-topbar-social { gap:.65rem; }
        }
            /* site-contact-topbar-motion */
        @keyframes topbarReveal {
            from { opacity:0; transform:translateY(-7px); filter:blur(1px); }
            to { opacity:1; transform:translateY(0); filter:blur(0); }
        }
        .site-topbar { font-size:.94rem; }
        .site-topbar-info a, .site-topbar-address,
        .site-topbar-social a {
            animation:topbarReveal .9s cubic-bezier(.2,.65,.3,1) both;
        }
        .site-topbar-info a:nth-child(2) { animation-delay:.14s; }
        .site-topbar-address { animation-delay:.28s; }
        .site-topbar-social a:nth-child(2) { animation-delay:.12s; }
        .site-topbar-social a:nth-child(3) { animation-delay:.24s; }
        @media (max-width:575.98px) {
            .site-topbar { font-size:.82rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            .site-topbar-info a, .site-topbar-address, .site-topbar-social a {
                animation:none !important;
            }
        }
            /* home-soft-hover-palette */
        .feature-box:hover, .notification-card:hover {
            background-color:#f8faff !important;
            background-image:
                radial-gradient(circle at 95% 5%,rgba(129,140,248,.14),transparent 38%),
                linear-gradient(135deg,#f8faff 0%,#fff8fc 100%) !important;
            color:#334155 !important;
            border-color:rgba(99,102,241,.25);
            box-shadow:0 14px 30px rgba(47,63,121,.13);
            transform:translateY(-4px);
        }
        .feature-box:hover h3, .notification-card:hover h4 { color:#4338a8 !important; }
        .feature-box:hover p, .notification-card:hover p,
        .notification-card:hover .notification-date { color:#64748b !important; }
        .feature-box:hover .feature-icon { transform:scale(1.04); }
        .notification-card:hover::before { width:4px; }
    </style>
</head>
<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Navigation -->
    <div class="site-topbar">
        <div class="container">
            <div class="site-topbar-info">
                <a href="tel:+919091850061"><i class="fas fa-phone-alt" aria-hidden="true"></i><span>+91 9091850061</span></a>
                <a href="mailto:childrenscorner85@gmail.com"><i class="fas fa-envelope" aria-hidden="true"></i><span>childrenscorner85@gmail.com</span></a>
                <span class="site-topbar-address"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span>Children's Corner, Sangrampur Road, Diamond Harbour - 743375</span></span>
            </div>
            <div class="site-topbar-social" aria-label="Social media links">
                <a href="https://facebook.com/childrenscorner" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                <a href="https://wa.me/919091850061" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                <a href="https://youtube.com/childrenscorner" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="./">
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
                    <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
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
    <section class="section home-about-section">
        <div class="container">
            <div class="section-title">
                <h2>About Us</h2>
                <div class="title-divider"></div>
                <p>Discover Ilhaam Mission High School - where academic excellence meets holistic student development</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <p class="lead home-about-lead">Admission for girls in Classes V-XII, fostering academic and moral growth.</p>
                    <p class="animate__animated animate__fadeInUp">In 1985, the esteemed educational institution established by Jaharul Haque Haldar Mahashay began its journey as the first nursery and KG school in the area. Currently, the institution is run by the Ilhaam Mission Trust, offering education alongside moral and ethical guidance. In addition to primary education, the institution also runs two sections — residential and non-residential — for social and moral development. Various educational initiatives have been launched to nurture and elevate students’ educational standards through residential programs, ensuring they grow in a beautiful environment.</p>
                    <p class="animate__animated animate__fadeInUp">From the 2025 academic year, Ilhaam Mission has introduced residential (8:00 AM – 8:00 PM) and non-residential (10:40 AM – 3:45 PM) education systems for talented female students.</p>
                    <div class="home-goals-heading animate__animated animate__fadeInUp">
                        <span class="home-goals-icon"><i class="fas fa-bullseye" aria-hidden="true"></i></span>
                        <h3>Goals and Objectives</h3>
                    </div>
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
                    <ul class="list-unstyled mt-3 quick-links-grid">
    <li class="mb-2"><a href="./"><i class="fas fa-angle-right me-2"></i>Home</a></li>
    <li class="mb-2"><a href="about"><i class="fas fa-angle-right me-2"></i>About Us</a></li>
    <li class="mb-2"><a href="notice"><i class="fas fa-angle-right me-2"></i>Notices</a></li>
    <li class="mb-2"><a href="admission"><i class="fas fa-angle-right me-2"></i>Admission</a></li>
    <li class="mb-2"><a href="gallery"><i class="fas fa-angle-right me-2"></i>Gallery</a></li>
    <li class="mb-2"><a href="donation"><i class="fas fa-angle-right me-2"></i>Donate</a></li>
    <li class="mb-2"><a href="success"><i class="fas fa-angle-right me-2"></i>Success Stories</a></li>
    <li class="mb-2"><a href="activities"><i class="fas fa-angle-right me-2"></i>Activities</a></li>
    <li class="mb-2"><a href="faculty"><i class="fas fa-angle-right me-2"></i>Faculty</a></li>
    <li class="mb-2"><a href="contact"><i class="fas fa-angle-right me-2"></i>Contact</a></li>
</ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row pt-3">
                <div class="col-md-12 text-center">
                    <p>&copy; 2026 Children's Corner Institution & Ilhaam Mission. All Rights Reserved.</p>
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
