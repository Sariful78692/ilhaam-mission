<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));

    // Validate inputs
    if (empty($name) || empty($email) || empty($mobile) || empty($subject) || empty($message)) {
        echo "<script>alert('All fields are required.'); window.location.href='index.php';</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href='index.php';</script>";
        exit;
    }

    // Prepare and execute query
    $sql = "INSERT INTO contact_messages (name, email, mobile, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $name, $email, $mobile, $subject, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error sending message. Please try again.'); window.location.href='index.php';</script>";
        error_log("Contact form submission error: " . $conn->error);
    }

    $stmt->close();
} else {
    header("Location: index.php");
    exit;
}

$conn->close();
