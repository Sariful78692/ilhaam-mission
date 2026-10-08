<?php
// Include database configuration
require_once dirname(__DIR__, 3) . '/config.php';

// Fetch contact information from the database
$sql_contact = "SELECT * FROM contact_info LIMIT 1"; // Assuming one record for simplicity
$result_contact = $conn->query($sql_contact);

if (!$result_contact) {
    die("Query failed: " . $conn->error);
}
$contact = $result_contact->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Children's Corner</title>
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
        .bengali-text {
            font-family: 'Noto Sans Bengali', sans-serif;
            line-height: 1.8;
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
        .about-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .mission-vision-card {
            background: var(--foreground);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            height: 100%;
        }
        .mission-vision-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .mission-vision-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border-radius: var(--radius);
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            color: #ffffff;
            font-size: 1.5rem;
            transition: var(--transition);
        }
        .mission-vision-card:hover .mission-vision-icon {
            transform: scale(1.1);
        }
        .director-card {
            background: var(--foreground);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .director-card::after {
            content: '\201D';
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 5rem;
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            opacity: 0.1;
        }
        .director-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--muted);
            margin-right: 1.5rem;
        }
        .director-signature {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            color: var(--accent);
            margin-top: 1rem;
        }
        .language-toggle-btn {
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            border: none;
            padding: 0.5rem 1rem;
            font-weight: 600;
            border-radius: var(--radius);
            color: #ffffff;
            margin-top: 1rem;
            display: inline-block;
            text-align: center;
            transition: var(--transition);
        }
        .language-toggle-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            animation: animate__pulse 0.5s;
        }
        .english-content, .bengali-content {
            transition: opacity 0.3s ease;
        }
        .english-content.hidden, .bengali-content.hidden {
            display: none;
            opacity: 0;
        }
        .faculty-link-btn {
            background: linear-gradient(45deg, var(--accent), var(--accent-hover));
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: var(--radius);
            color: #ffffff;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            display: block;
            text-align: center;
            transition: var(--transition);
        }
        .faculty-link-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
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
            .director-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            .section {
                padding: 2rem 0;
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
            /* About page text motion and Goals & Objectives panel */
        .about-content p, .mission-vision-card p, .director-card p,
        .goals-objectives h2, .goals-objectives h3, .goals-objectives p,
        .goals-objectives li {
            transition:color .25s ease, transform .25s ease, text-shadow .25s ease;
        }
        .about-content p:hover, .mission-vision-card p:hover, .director-card p:hover {
            color:var(--accent); transform:translateX(3px);
        }
        .goals-objectives { background:linear-gradient(135deg,#f4f7ff,#fff); }
        .goals-objectives-panel {
            position:relative; height:100%; padding:2rem 1.75rem;
            border:1px solid #e0e8fb; border-top:4px solid var(--accent);
            border-radius:16px; background:#fff; box-shadow:0 12px 30px rgba(29,56,107,.09);
            transition:transform .3s ease, box-shadow .3s ease;
        }
        .goals-objectives-panel:hover { transform:translateY(-6px); box-shadow:0 18px 38px rgba(29,56,107,.15); }
        .goals-objectives-panel h3 { color:var(--accent); font-weight:700; margin-bottom:1rem; }
        .goals-objectives-panel ul { list-style:none; padding:0; margin:0; }
        .goals-objectives-panel li { position:relative; padding:.55rem 0 .55rem 1.65rem; color:var(--text); line-height:1.65; }
        .goals-objectives-panel li::before { content:'\f00c'; font-family:'Font Awesome 6 Free'; font-weight:900; color:var(--accent); position:absolute; left:0; top:.7rem; }
        .goals-objectives-panel li:hover { color:var(--accent); transform:translateX(3px); }
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
            /* leadership-images-top */
        .leadership-images-top .director-card > .d-flex {
            flex-direction:column !important;
            align-items:center !important;
        }
        .leadership-images-top .director-image {
            display:block;
            flex:0 0 auto;
            margin:0 0 1.25rem !important;
        }
        .leadership-images-top .director-card .flex-grow-1 {
            width:100%;
            margin-left:0 !important;
        }
            /* Bigger portraits and centered names for both message sections */
        .leadership-images-top .director-image,
        .founder-message-images-top .director-image {
            width:170px; height:170px; margin:0 0 1.3rem !important;
        }
        .leadership-images-top .director-card h3,
        .leadership-images-top .director-signature,
        .leadership-images-top .director-card .text-muted,
        .founder-message-images-top .director-card h3,
        .founder-message-images-top .director-signature,
        .founder-message-images-top .director-card .text-muted {
            text-align:center;
        }
        .founder-message-images-top .director-card > .d-flex {
            flex-direction:column !important;
            align-items:center !important;
        }
        .founder-message-images-top .director-card .flex-grow-1 {
            width:100%; margin-left:0 !important;
        }
        @media (max-width:575.98px) {
            .leadership-images-top .director-image,
            .founder-message-images-top .director-image { width:145px; height:145px; }
        }
            /* about-message-name-style */
        .leadership-images-top .director-card h3,
        .founder-message-images-top .director-card h3 {
            position:relative;
            display:table;
            margin:0 auto 1.25rem !important;
            padding:0 12px 12px;
            font-size:clamp(1.45rem,2.5vw,1.8rem);
            font-weight:750;
            letter-spacing:.015em;
            line-height:1.35;
            background:linear-gradient(100deg,#3a0ca3,#4361ee 55%,#c02683);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
            text-shadow:0 5px 18px rgba(67,97,238,.12);
        }
        .leadership-images-top .director-card h3::after,
        .founder-message-images-top .director-card h3::after {
            content:'';
            position:absolute;
            width:54px;
            height:3px;
            left:50%;
            bottom:0;
            transform:translateX(-50%);
            border-radius:5px;
            background:linear-gradient(90deg,#4361ee,#e879b5);
            transition:width .35s ease;
        }
        .leadership-images-top .director-card h3:hover::after,
        .founder-message-images-top .director-card h3:hover::after { width:92px; }
        .leadership-images-top .director-signature,
        .founder-message-images-top .director-signature {
            color:#4b2ac4;
            font-size:1.9rem;
            transition:color .35s ease, transform .35s ease;
        }
        .leadership-images-top .director-signature:hover,
        .founder-message-images-top .director-signature:hover {
            color:#b02683;
            transform:translateY(-2px);
        }
        @keyframes aboutSlowReveal {
            from { opacity:0; transform:translateY(13px); }
            to { opacity:1; transform:translateY(0); }
        }
        .leadership-images-top .animate__animated,
        .founder-message-images-top .animate__animated,
        .leadership-images-top :is(h3,p,a,li,span,button),
        .founder-message-images-top :is(h3,p,a,li,span,button) {
            animation-duration:1.2s !important;
            animation-timing-function:cubic-bezier(.2,.65,.3,1) !important;
            transition-duration:.35s;
        }
        .leadership-images-top .director-card h3,
        .founder-message-images-top .director-card h3,
        .leadership-images-top .director-signature,
        .founder-message-images-top .director-signature {
            animation-name:aboutSlowReveal !important;
        }
        @media (prefers-reduced-motion: reduce) {
            .leadership-images-top .animate__animated,
            .founder-message-images-top .animate__animated,
            .leadership-images-top :is(h3,p,a,li,span,button),
            .founder-message-images-top :is(h3,p,a,li,span,button) {
                animation:none !important;
                transition:none !important;
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
    </style>
</head>
<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <!-- Notification Container -->
    <div class="notification-container animate__animated" data-animate="fadeIn" id="notificationContainer"></div>

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
                        <span class="logo-tagline">Empowering Young Minds for a Bright Future</span>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about">About Us</a></li>
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

    <!-- Breadcrumb Section -->
    <section class="breadcrumb-section animate__animated" data-animate="fadeIn">
        <div class="container">
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
    </div>            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item animate__animated" data-animate="bounceIn" data-delay="0.1s"><a href="./">Home</a></li>
                    <li class="breadcrumb-item active animate__animated" data-animate="bounceIn" data-delay="0.2s" aria-current="page">About Us</li>
                </ol>
            </nav>
            <h1 class="text-white animate__animated" data-animate="fadeInDown">About Us</h1>
            <p class="lead text-white mb-0 animate__animated" data-animate="fadeInUp">Empowering Young Minds for a Bright Future</p>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="section animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="section-title text-lg-start animate__animated" data-animate="fadeInDown">
                        <h2>Who We Are</h2>
                        <div class="title-divider mx-lg-0"></div>
                    </div>
                    <div class="about-content animate__animated" data-animate="fadeInUp">
                        <p>ILHAAM MISSION aims to work for a holistic development of vulnerable and marginalized population, mainly the Minorities– emphasizing on Education, Health and Livelihood issues and the basic Human Rights including Public Services those are entitled for them.</p>
                        <p>ILHAAM MISSION aim is to empower vulnerable communities for self-sufficient and self-sustained growth involving them in participatory learning & planning in democratic functioning. Promotion of locally viable enterprises in the course of empowerment endeavor is also priorities of the Trust.</p>
                        <p>ILHAAM MISSION work for collective action in reducing injustice & discrimination within the society, which it sets in its milestone.</p>
                        <p>ILHAAM MISSION has a trust on its own philosophy of mankind and values of human relations and collective thinking which will give the wide range of strength to fight against the challenges are kindizing common people’s life.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://ilhaammission.org/photos/b.png" alt="Children's Corner" class="img-fluid rounded shadow animate__animated" data-animate="fadeInRight">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="section bg-light animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Our Mission & Vision</h2>
                <div class="title-divider"></div>
                <p>Guiding principles that drive our commitment to excellence</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mission-vision-card animate__animated" data-animate="fadeInUp" data-delay="0.1s">
                        <div class="mission-vision-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Our Mission</h3>
                        <p>ILHAAM MISSION is a human relation organization dedicated to fight against usury, bondage, migration, gender bias, child abuse and deprivation of poor and other vulnerable and marginalize groups.</p>
                        <p>ILHAAM MISSION promotes the practice of good governance and understanding among all groups, classes and cultures through education, advocacy and conflict resolution.</p>
                        <p>ILHAAM MISSION believes that the vulnerable and marginalized groups, specifically the women, expected to be empowered in the society on various counts within the basic premises.</p>
                        <p>ILHAAM MISSION through its quality education set ups provides the knowledge and skills for sustainable livelihoods development.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mission-vision-card animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                        <div class="mission-vision-icon animate__animated" data-animate="zoomIn">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Our Vision</h3>
                        <p>ILHAAM MISSION aims to empower vulnerable communities for self-sufficient and self-sustained growth involving them in participatory learning & planning in democratic functioning.</p>
                        <p>ILHAAM MISSION work for collective action in reducing injustice & discrimination within the society, which it sets in its milestone.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Goals and Objectives Section -->
    <section class="section goals-objectives">
        <div class="container">
            <div class="section-title">
                <h2>Goals &amp; Objectives</h2>
                <div class="title-divider"></div>
                <p>Our priorities for building stronger, more empowered communities</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-5">
                    <article class="goals-objectives-panel">
                        <h3><i class="fas fa-bullseye me-2" aria-hidden="true"></i>Our Goal</h3>
                        <p>To empower girls and marginalized communities through quality education, holistic development, and equal opportunities for a self-reliant future.</p>
                    </article>
                </div>
                <div class="col-lg-7">
                    <article class="goals-objectives-panel">
                        <h3><i class="fas fa-list-check me-2" aria-hidden="true"></i>Our Objectives</h3>
                        <ul>
                            <li>Provide an inclusive, supportive learning environment that helps every student reach her potential.</li>
                            <li>Promote academic excellence alongside personal, social, and moral development.</li>
                            <li>Expand access to education and practical skills for girls and underserved communities.</li>
                            <li>Encourage dignity, equal opportunity, and active participation in community life.</li>
                        </ul>
                    </article>
                </div>
            </div>
        </div>
    </section>
    <!-- Leadership Messages Section -->
    <section class="section leadership-images-top animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Leadership Messages</h2>
                <div class="title-divider"></div>
                <p>Words from our leadership</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="director-card animate__animated" data-animate="fadeInUp" data-delay="0.1s">
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <img src="https://ilhaammission.org/photos/c.png" alt="Sabir Hossain Halder" class="director-image animate__animated" data-animate="fadeInLeft">
                            <div class="flex-grow-1 ms-md-4">
                                <h3 class="mb-3 animate__animated" data-animate="fadeIn">Sabir Hossain Halder</h3>
                                <div class="english-content" data-lang="en">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">At Children's Corner Institution Mission, we strive to create a nurturing environment that fosters academic excellence and personal growth. Our vision is to empower students to achieve their highest potential while encouraging their unique talents.</p>
                                </div>
                                <div class="bengali-content hidden bengali-text" data-lang="bn">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">চিলড্রেনস কর্নার ইনস্টিটিউশন মিশনে, আমরা এমন একটি পোষণমূলক পরিবেশ তৈরি করার জন্য প্রয়াসী যা শিক্ষাগত উৎকর্ষতা এবং ব্যক্তিগত বিকাশকে উৎসাহিত করে। আমাদের দৃষ্টিভঙ্গি হলো ছাত্রদের তাদের সর্বোচ্চ সম্ভাবনা অর্জনের জন্য ক্ষমতায়ন করা এবং তাদের অনন্য প্রতিভাকে উৎসাহিত করা।</p>
                                </div>
                                <button class="language-toggle-btn animate__animated" data-animate="fadeInUp" data-animate-hover="pulse" data-delay="0.2s" data-lang="en">বাংলা</button>
                                <div class="director-signature animate__animated" data-animate="fadeInUp" data-delay="0.3s">Sabir Hossain Halder</div>
                                <p class="text-muted mt-2 animate__animated" data-animate="fadeInUp" data-delay="0.4s">Director, Children's Corner Institution Mission</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="director-card animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <img src="https://ilhaammission.org/photos/d.png" alt="Abdul Amil Al Hassan" class="director-image animate__animated" data-animate="fadeInLeft">
                            <div class="flex-grow-1 ms-md-4">
                                <h3 class="mb-3 animate__animated" data-animate="fadeIn">Abdul Amil Al Hassan</h3>
                                <div class="english-content" data-lang="en">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">At Children's Corner Institution & Ilhaam Mission, we are committed to providing a supportive and enriching environment that promotes academic excellence and holistic development. Our vision is to empower students to reach their full potential while cultivating their individual talents.</p>
                                </div>
                                <div class="bengali-content hidden bengali-text" data-lang="bn">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">চিলড্রেনস কর্নার ইনস্টিটিউশন ও ইলহাম মিশনে, আমরা এমন একটি সহায়ক ও সমৃদ্ধ পরিবেশ প্রদানের জন্য প্রতিশ্রুতিবদ্ধ যা শিক্ষাগত উৎকর্ষতা এবং সামগ্রিক বিকাশকে উৎসাহিত করে। আমাদের দৃষ্টিভঙ্গি হলো ছাত্রদের তাদের পূর্ণ সম্ভাবনায় পৌঁছানোর জন্য ক্ষমতায়ন করা এবং তাদের ব্যক্তিগত প্রতিভার বিকাশ সাধন করা।</p>
                                </div>
                                <button class="language-toggle-btn animate__animated" data-animate="fadeInUp" data-animate-hover="pulse" data-delay="0.2s" data-lang="en">বাংলা</button>
                                <div class="director-signature animate__animated" data-animate="fadeInUp" data-delay="0.3s">Abdul Amil Al Hassan</div>
                                <p class="text-muted mt-2 animate__animated" data-animate="fadeInUp" data-delay="0.4s">In-Charge of High School & Ilhaam Mission</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Founder's Message Section -->
    <section class="section bg-light founder-message-images-top animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Founder's Message</h2>
                <div class="title-divider"></div>
                <p>Words from our founder</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="director-card animate__animated" data-animate="fadeInUp" data-delay="0.1s">
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <img src="https://ilhaammission.org/photos/a.png" alt="Sajida Halder" class="director-image animate__animated" data-animate="fadeInLeft">
                            <div class="flex-grow-1 ms-md-4">
                                <h3 class="mb-3 animate__animated" data-animate="fadeIn">Sajida Halder</h3>
                                <div class="english-content" data-lang="en">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">It's a great pleasure to be a part of Children's Corner, a trust for educational and social development. My husband, the founder of Children's Corner Institution, was my inspiration and my youngest son Sabir Hossain brought me here in this field in 2015. It was as if a woman of the "Home" or better to say a homemaker was made a leader to lead from the front in the external world to strengthen the institutions in absence of the general, known as Z.H.H Sir (Boro Sir, as the staff used to call him). At present, the trust is running and maintaining several institutions, namely:</p>
                                    <ul class="animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                                        <li>Children's Corner Institution, Nursery - Madhyamik, Estd. 1985 by Z.H.Halder</li>
                                        <li>Ilhaam Mission (V-XII) Only for girls, Estd. 2023</li>
                                        <li>Computer Education Coaching Centre, Estd. 2013</li>
                                        <li>Student's Corner Coaching Centre, Estd. 2010</li>
                                        <li>Creative Corner Training Centre for Art & Craft, Yoga, Dance, Personality Development, Estd. 2021</li>
                                        <li>Zahurul Hoque Memorial Library (Open to All 11am-5pm), Estd. 2016</li>
                                    </ul>
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.3s">Please pray for us and stand by us so that we may look forward to building a strong nation.</p>
                                </div>
                                <div class="bengali-content hidden bengali-text" data-lang="bn">
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.1s">চিলড্রেনস কর্নারের একটি অংশ হতে পেরে আমার জন্য অত্যন্ত আনন্দের। এটি শিক্ষা ও সামাজিক উন্নয়নের জন্য একটি ট্রাস্ট। আমার স্বামী, চিলড্রেনস কর্নার ইনস্টিটিউশনের প্রতিষ্ঠাতা, আমার প্রেরণা ছিলেন এবং আমার কনিষ্ঠ পুত্র সাবির হোসেন ২০১৫ সালে আমাকে এই ক্ষেত্রে নিয়ে এসেছেন। এটি এমন ছিল যেন একজন গৃহিণীকে বাইরের জগতে নেতৃত্ব দেওয়ার জন্য নেতা হিসেবে তৈরি করা হয়েছে, যিনি জেনারেল, জে.এইচ. হালদার স্যার (স্টাফরা যাকে বড়ো স্যার বলে ডাকতেন) এর অনুপস্থিতিতে প্রতিষ্ঠানগুলিকে শক্তিশালী করেছেন। বর্তমানে ট্রাস্টটি নিম্নলিখিত প্রতিষ্ঠানগুলি পরিচালনা ও রক্ষণাবেক্ষণ করছে:</p>
                                    <ul class="animate__animated" data-animate="fadeInUp" data-delay="0.2s">
                                        <li>চিলড্রেনস কর্নার ইনস্টিটিউশন, নার্সারি - মাধ্যমিক, প্রতিষ্ঠিত ১৯৮৫, জে.এইচ. হালদার দ্বারা</li>
                                        <li>ইলহাম মিশন (পঞ্চম-দ্বাদশ), শুধুমাত্র মেয়েদের জন্য, প্রতিষ্ঠিত ২০২৩</li>
                                        <li>কম্পিউটার এডুকেশন কোচিং সেন্টার, প্রতিষ্ঠিত ২০১৩</li>
                                        <li>স্টুডেন্টস কর্নার কোচিং সেন্টার, প্রতিষ্ঠিত ২০১০</li>
                                        <li>ক্রিয়েটিভ কর্নার ট্রেনিং সেন্টার ফর আর্ট অ্যান্ড ক্রাফট, যোগ, নৃত্য, ব্যক্তিত্ব বিকাশ, প্রতিষ্ঠিত ২০২১</li>
                                        <li>জহুরুল হক মেমোরিয়াল লাইব্রেরি (সকলের জন্য উন্মুক্ত, সকাল ১১টা-বিকেল ৫টা), প্রতিষ্ঠিত ২০১৬</li>
                                    </ul>
                                    <p class="lead animate__animated" data-animate="fadeInUp" data-delay="0.3s">আমাদের জন্য প্রার্থনা করুন এবং আমাদের পাশে থাকুন যাতে আমরা একটি শক্তিশালী জাতি গড়ে তুলতে পারি।</p>
                                </div>
                                <button class="language-toggle-btn animate__animated" data-animate="fadeInUp" data-animate-hover="pulse" data-delay="0.4s" data-lang="en">বাংলা</button>
                                <div class="director-signature animate__animated" data-animate="fadeInUp" data-delay="0.5s">Sajida Halder</div>
                                <p class="text-muted mt-2 animate__animated" data-animate="fadeInUp" data-delay="0.6s">Founder, Children's Corner Institution</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Link Section -->
    <section class="section bg-light animate__animated" data-animate="fadeIn">
        <div class="container">
            <div class="section-title animate__animated" data-animate="fadeInDown">
                <h2>Meet Our Faculty</h2>
                <div class="title-divider"></div>
                <p>Discover our team of expert educators</p>
            </div>
            <a href="faculty.php" class="faculty-link-btn animate__animated" data-animate="fadeInUp" data-animate-hover="pulse">View Our Expert Faculty</a>
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
                <p class="animate__animated" data-animate="fadeInUp">&copy; 2026 Children's Corner Institution & Ilhaam Mission. All Rights Reserved.</p>
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
            const elements = document.querySelectorAll('.animate-up, [data-animate]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        if (element.classList.contains('animate-up')) {
                            element.classList.add('animated');
                        } else {
                            const animation = element.getAttribute('data-animate');
                            const delay = element.getAttribute('data-delay') || '0s';
                            element.classList.add('animate__animated', 'animate__' + animation);
                            element.style.animationDelay = delay;
                        }
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

        // Language Toggle Handler
        function handleLanguageToggle() {
            const toggleButtons = document.querySelectorAll('.language-toggle-btn');
            toggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const card = button.closest('.director-card');
                    const englishContent = card.querySelector('.english-content');
                    const bengaliContent = card.querySelector('.bengali-content');
                    const currentLang = button.getAttribute('data-lang');

                    if (currentLang === 'en') {
                        englishContent.classList.add('hidden');
                        bengaliContent.classList.remove('hidden');
                        bengaliContent.classList.add('animate__animated', 'animate__fadeIn');
                        button.setAttribute('data-lang', 'bn');
                        button.textContent = 'English';
                    } else {
                        bengaliContent.classList.add('hidden');
                        englishContent.classList.remove('hidden');
                        englishContent.classList.add('animate__animated', 'animate__fadeIn');
                        button.setAttribute('data-lang', 'en');
                        button.textContent = 'বাংলা';
                    }

                    // Remove animation class after animation ends
                    englishContent.addEventListener('animationend', () => {
                        englishContent.classList.remove('animate__animated', 'animate__fadeIn');
                    }, { once: true });
                    bengaliContent.addEventListener('animationend', () => {
                        bengaliContent.classList.remove('animate__animated', 'animate__fadeIn');
                    }, { once: true });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleAnimations();
            handleLanguageToggle();
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
