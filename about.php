<?php
// Include database configuration
require_once 'config.php';

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
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item animate__animated" data-animate="bounceIn" data-delay="0.1s"><a href="index.php">Home</a></li>
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

    <!-- Leadership Messages Section -->
    <section class="section animate__animated" data-animate="fadeIn">
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
    <section class="section bg-light animate__animated" data-animate="fadeIn">
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