<?php
// admission.php
require_once 'config.php';

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Start output buffering
ob_start();

// Start session for CSRF token
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Get and validate JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
        error_log('Invalid JSON input: ' . json_last_error_msg());
        exit;
    }

    // Log input for debugging
    error_log('Received POST input: ' . json_encode($input));

    // Validate CSRF token
    $csrf_token = $input['csrf_token'] ?? '';
    if ($csrf_token !== $_SESSION['csrf_token']) {
        ob_end_clean();
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        error_log('Invalid CSRF token: Sent=' . $csrf_token . ', Expected=' . $_SESSION['csrf_token']);
        exit;
    }

    // Collect and sanitize input
    $fields = [
        'roll_number', 'applied_class', 'academic_session', 'applied_for', 'student_name', 'dob', 'gender',
        'religion', 'category', 'aadhar', 'village', 'post_office', 'police_station', 'district', 'pin_code',
        'state', 'contact_number', 'whatsapp_number', 'father_name', 'father_qualification', 'father_occupation',
        'father_monthly_income', 'mother_name', 'mother_qualification', 'mother_occupation', 'mother_monthly_income',
        'local_guardian_name', 'local_guardian_address', 'relation_with_guardian', 'guardian_contact_number',
        'previous_institution', 'payment_id', 'order_id', 'status', 'declaration'
    ];
    $formData = [];
    foreach ($fields as $field) {
        $formData[$field] = filter_var($input[$field] ?? ($field === 'status' ? 'pending' : ($field === 'declaration' ? '' : 'NIL')), FILTER_SANITIZE_STRING);
    }

    // Validate required fields
    $required_fields = [
        'roll_number', 'applied_class', 'academic_session', 'applied_for', 'student_name', 'dob', 'gender',
        'religion', 'category', 'aadhar', 'village', 'post_office', 'police_station', 'district', 'pin_code',
        'state', 'contact_number', 'whatsapp_number', 'father_name', 'mother_name', 'previous_institution',
        'payment_id', 'order_id', 'declaration'
    ];
    foreach ($required_fields as $field) {
        if (empty($formData[$field]) || $formData[$field] === 'NIL') {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
            error_log("Missing required field: $field");
            exit;
        }
    }

    // Validate formats
    if (!preg_match('/^\d{12}$/', $formData['aadhar'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid Aadhar number (must be 12 digits)']);
        error_log('Invalid Aadhar number: ' . $formData['aadhar']);
        exit;
    }
    if (!preg_match('/^[6-9]\d{9}$/', $formData['contact_number']) || !preg_match('/^[6-9]\d{9}$/', $formData['whatsapp_number'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid phone or WhatsApp number (must be 10-digit Indian number starting with 6-9)']);
        error_log('Invalid phone/WhatsApp number: contact=' . $formData['contact_number'] . ', whatsapp=' . $formData['whatsapp_number']);
        exit;
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $formData['dob'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid date of birth format (YYYY-MM-DD)']);
        error_log('Invalid DOB: ' . $formData['dob']);
        exit;
    }
    if (!in_array($formData['gender'], ['Male', 'Female', 'Other'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid gender value']);
        error_log('Invalid gender: ' . $formData['gender']);
        exit;
    }
    if (!in_array($formData['category'], ['GEN', 'OBC-A', 'OBC-B', 'PC'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid category value']);
        error_log('Invalid category: ' . $formData['category']);
        exit;
    }
    if (!in_array($formData['applied_for'], ['Hosteller', 'Day Scholar'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid applied_for value']);
        error_log('Invalid applied_for: ' . $formData['applied_for']);
        exit;
    }
    if ($formData['declaration'] !== 'agree') {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'You must agree to the declaration']);
        error_log('Declaration not agreed');
        exit;
    }

    // Check for duplicate Aadhar or roll number
    $stmt = $conn->prepare("SELECT aadhar, roll_number FROM admissions WHERE aadhar = ? OR roll_number = ?");
    if (!$stmt) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
        error_log('Database prepare error (duplicate check): ' . $conn->error);
        $conn->close();
        exit;
    }
    $stmt->bind_param("ss", $formData['aadhar'], $formData['roll_number']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $error_message = $row['aadhar'] === $formData['aadhar'] ? 'Aadhar number already exists' : 'Roll number already exists';
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $error_message]);
        error_log($error_message . ': ' . json_encode($row));
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Verify payment status
    $stmt = $conn->prepare("SELECT status FROM payments WHERE order_id = ? AND payment_id = ?");
    if (!$stmt) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
        error_log('Database prepare error (payment check): ' . $conn->error);
        $conn->close();
        exit;
    }
    $stmt->bind_param("ss", $formData['order_id'], $formData['payment_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0 || $result->fetch_assoc()['status'] !== 'success') {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Payment not verified']);
        error_log('Payment not verified for order_id: ' . $formData['order_id'] . ', payment_id: ' . $formData['payment_id']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Insert data into admissions table
    $stmt = $conn->prepare("
        INSERT INTO admissions (
            roll_number, applied_class, academic_session, applied_for, student_name, dob, gender, religion, category, aadhar,
            village, post_office, police_station, district, pin_code, state, contact_number, whatsapp_number,
            father_name, father_qualification, father_occupation, father_monthly_income,
            mother_name, mother_qualification, mother_occupation, mother_monthly_income,
            local_guardian_name, local_guardian_address, relation_with_guardian, guardian_contact_number,
            previous_institution, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
        error_log('Database prepare error (insert): ' . $conn->error);
        $conn->close();
        exit;
    }
    $stmt->bind_param(
        "ssssssssssssssssssssssssssssssss",
        $formData['roll_number'], $formData['applied_class'], $formData['academic_session'], $formData['applied_for'],
        $formData['student_name'], $formData['dob'], $formData['gender'], $formData['religion'], $formData['category'],
        $formData['aadhar'], $formData['village'], $formData['post_office'], $formData['police_station'], $formData['district'],
        $formData['pin_code'], $formData['state'], $formData['contact_number'], $formData['whatsapp_number'],
        $formData['father_name'], $formData['father_qualification'], $formData['father_occupation'], $formData['father_monthly_income'],
        $formData['mother_name'], $formData['mother_qualification'], $formData['mother_occupation'], $formData['mother_monthly_income'],
        $formData['local_guardian_name'], $formData['local_guardian_address'], $formData['relation_with_guardian'],
        $formData['guardian_contact_number'], $formData['previous_institution'], $formData['status']
    );

    if ($stmt->execute()) {
        // Update payments table with roll_number
        $stmt_payment = $conn->prepare("UPDATE payments SET roll_number = ? WHERE order_id = ?");
        if (!$stmt_payment) {
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
            error_log('Database prepare error (payment update): ' . $conn->error);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt_payment->bind_param("ss", $formData['roll_number'], $formData['order_id']);
        $stmt_payment->execute();
        $stmt_payment->close();

        ob_end_clean();
        echo json_encode(['status' => 'success', 'message' => 'Admission submitted successfully!', 'roll_number' => $formData['roll_number']]);
        error_log('Admission saved successfully: roll_number=' . $formData['roll_number']);
    } else {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        error_log('Database insert error: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Fetch contact information for footer
$sql_contact = "SELECT * FROM contact_info LIMIT 1";
$result_contact = $conn->query($sql_contact);
if (!$result_contact) {
    error_log('Contact query failed: ' . $conn->error);
    $contact = ['phone_numbers' => '9091850061, 9163588008', 'email' => 'childrenscorner85@gmail.com', 'whatsapp_number' => '9163588008'];
} else {
    $contact = $result_contact->fetch_assoc() ?: ['phone_numbers' => '9091850061, 9163588008', 'email' => 'childrenscorner85@gmail.com', 'whatsapp_number' => '9163588008'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <title>Admission Form - Ilhaam Mission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}
.form-control, .form-select, .form-check-input {
    border-radius: var(--radius);
    border: 2px solid var(--border);
    padding: 0.75rem;
    transition: var(--transition);
}
.form-control:focus, .form-select:focus, .form-check-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 0.2rem rgba(58, 12, 163, 0.25);
}
.form-control.is-invalid, .form-select.is-invalid, .form-check-input.is-invalid {
    border-color: #dc3545;
}
.invalid-feedback {
    color: #dc3545;
    font-size: 0.9rem;
}
.btn-primary {
    background: var(--gradient-accent);
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: var(--radius);
    color: #ffffff;
    transition: var(--transition);
}
.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow);
}
.btn-primary:disabled {
    background: var(--muted);
    cursor: not-allowed;
}
.notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 2000;
    max-width: 320px;
    padding: 0.75rem;
    border-radius: var(--radius);
    color: #fff;
}
.notification.success {
    background-color: #28a745;
}
.notification.error {
    background-color: #dc3545;
}
.confirmation-section {
    background: var(--gradient-accent);
    color: #ffffff;
    padding: 4rem 0;
    text-align: center;
    min-height: 100vh;
    display: none;
}
.confirmation-section.active {
    display: block;
}
.confirmation-card {
    background: var(--foreground);
    border-radius: var(--radius);
    padding: 2rem;
    max-width: 600px;
    margin: 0 auto;
    color: var(--text);
    box-shadow: var(--shadow);
}
.confirmation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}
.roll-number-badge {
    background: var(--gradient-accent);
    color: #ffffff;
    padding: 0.75rem;
    border-radius: var(--radius);
    font-weight: 600;
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
    .card {
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
    .card {
        padding: 1rem;
    }
    .form-control, .form-select {
        font-size: 0.85rem;
    }
    .btn-primary {
        font-size: 0.85rem;
    }
}
    </style>
</head>
<body>
    <div class="notification" id="notification" style="display: none;"></div>
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
                    <li class="nav-item"><a class="nav-link active" href="admission">Admission</a></li>
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
    <section class="section" id="formSection">
        <div class="container">
            <div class="section-title">
                <h2>Admission Form (Classes V-XII)</h2>
                <p>Complete the details to apply for admission at Ilhaam Mission</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <form id="admissionForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="roll_number" id="roll_number">
                        <h5>Student Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="applied_class" class="form-label">Applied for Class *</label>
                                <select class="form-select" id="applied_class" name="applied_class" required>
                                    <option value="">Select Class</option>
                                    <?php for ($i = 5; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i == 11 ? 'XI' : ($i == 12 ? 'XII' : 'Class ' . $i); ?>">
                                            <?php echo $i == 11 ? 'XI' : ($i == 12 ? 'XII' : 'Class ' . $i); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="academic_session" class="form-label">Academic Session *</label>
                                <input type="text" class="form-control" id="academic_session" name="academic_session" required placeholder="e.g., 2025-2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Applied For *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="applied_for" id="hosteller" value="Hosteller" required>
                                        <label class="form-check-label" for="hosteller">Hosteller</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="applied_for" id="day_scholar" value="Day Scholar">
                                        <label class="form-check-label" for="day_scholar">Day Scholar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="student_name" class="form-label">Student's Name *</label>
                                <input type="text" class="form-control" id="student_name" name="student_name" required placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date of Birth (YYYY-MM-DD) *</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="Other">
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label">Religion *</label>
                                <input type="text" class="form-control" id="religion" name="religion" required placeholder="Enter religion">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="gen" value="GEN" required>
                                        <label class="form-check-label" for="gen">GEN</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="obc_a" value="OBC-A">
                                        <label class="form-check-label" for="obc_a">OBC-A</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="obc_b" value="OBC-B">
                                        <label class="form-check-label" for="obc_b">OBC-B</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="pc" value="PC">
                                        <label class="form-check-label" for="pc">PC</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="aadhar" class="form-label">Aadhar Number *</label>
                                <input type="text" class="form-control" id="aadhar" name="aadhar" required placeholder="Enter 12-digit Aadhar number" maxlength="12" pattern="\d{12}">
                            </div>
                        </div>
                        <h5 class="mt-4">Address Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="village" class="form-label">Village *</label>
                                <input type="text" class="form-control" id="village" name="village" required placeholder="Enter village">
                            </div>
                            <div class="col-md-6">
                                <label for="post_office" class="form-label">Post Office *</label>
                                <input type="text" class="form-control" id="post_office" name="post_office" required placeholder="Enter post office">
                            </div>
                            <div class="col-md-6">
                                <label for="police_station" class="form-label">Police Station *</label>
                                <input type="text" class="form-control" id="police_station" name="police_station" required placeholder="Enter police station">
                            </div>
                            <div class="col-md-6">
                                <label for="district" class="form-label">District *</label>
                                <input type="text" class="form-control" id="district" name="district" required placeholder="Enter district">
                            </div>
                            <div class="col-md-6">
                                <label for="pin_code" class="form-label">Pin Code *</label>
                                <input type="text" class="form-control" id="pin_code" name="pin_code" required placeholder="Enter pin code">
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state" required placeholder="Enter state">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" required placeholder="Enter 10-digit number">
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_number" class="form-label">WhatsApp Number *</label>
                                <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number" required placeholder="Enter 10-digit number">
                            </div>
                        </div>
                        <h5 class="mt-4">Parent/Guardian Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="father_name" class="form-label">Father's Name *</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" required placeholder="Enter father's name">
                            </div>
                            <div class="col-md-6">
                                <label for="father_qualification" class="form-label">Father's Qualification</label>
                                <input type="text" class="form-control" id="father_qualification" name="father_qualification" placeholder="Enter qualification or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="father_occupation" class="form-label">Father's Occupation</label>
                                <input type="text" class="form-control" id="father_occupation" name="father_occupation" placeholder="Enter occupation or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="father_monthly_income" class="form-label">Father's Monthly Income</label>
                                <input type="text" class="form-control" id="father_monthly_income" name="father_monthly_income" placeholder="Enter income or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="mother_name" class="form-label">Mother's Name *</label>
                                <input type="text" class="form-control" id="mother_name" name="mother_name" required placeholder="Enter mother's name">
                            </div>
                            <div class="col-md-6">
                                <label for="mother_qualification" class="form-label">Mother's Qualification</label>
                                <input type="text" class="form-control" id="mother_qualification" name="mother_qualification" placeholder="Enter qualification or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="mother_occupation" class="form-label">Mother's Occupation</label>
                                <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Enter occupation or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="mother_monthly_income" class="form-label">Mother's Monthly Income</label>
                                <input type="text" class="form-control" id="mother_monthly_income" name="mother_monthly_income" placeholder="Enter income or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="local_guardian_name" class="form-label">Local Guardian's Name</label>
                                <input type="text" class="form-control" id="local_guardian_name" name="local_guardian_name" placeholder="Enter name or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="local_guardian_address" class="form-label">Local Guardian's Address</label>
                                <input type="text" class="form-control" id="local_guardian_address" name="local_guardian_address" placeholder="Enter address or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="relation_with_guardian" class="form-label">Relation with Guardian</label>
                                <input type="text" class="form-control" id="relation_with_guardian" name="relation_with_guardian" placeholder="Enter relation or NIL">
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_contact_number" class="form-label">Guardian's Contact Number</label>
                                <input type="tel" class="form-control" id="guardian_contact_number" name="guardian_contact_number" placeholder="Enter number or NIL">
                            </div>
                        </div>
                        <h5 class="mt-4">Educational Background</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="previous_institution" class="form-label">Previous Institution *</label>
                                <input type="text" class="form-control" id="previous_institution" name="previous_institution" required placeholder="Enter name and address of previous institution">
                            </div>
                        </div>
                        <h5 class="mt-4">Declaration</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration" name="declaration" value="agree" required>
                                    <label class="form-check-label" for="declaration">
                                        I declare that all information provided is true to the best of my knowledge.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submitButton">Proceed to Payment (₹100)</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="confirmation-section" id="confirmationSection">
        <div class="container">
            <div class="confirmation-card">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                <h2>Admission Submitted Successfully!</h2>
                <div class="roll-number-badge">
                    Roll Number: <span id="finalRollNumber"></span>
                </div>
                <h5>Your Admission Details</h5>
                <ul class="details-list" id="detailsList"></ul>
                <div class="mt-4">
                    <button class="btn btn-primary" onclick="printConfirmation()">Print Confirmation</button>
                    <button class="btn btn-primary" onclick="location.reload()">New Admission</button>
                </div>
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
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admission.js"></script>
</body>
</html>