<?php
// config.php

// Absolute path to .env file
$envPath = '.env';

if (!file_exists($envPath)) {
    die(".env file not found at: " . $envPath);
}

// Load .env
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue; // skip comments
    list($name, $value) = explode('=', $line, 2);
    $_ENV[trim($name)] = trim($value);
    putenv("$name=$value");
}

// DB credentials
$servername = getenv('DB_HOST');
$username   = getenv('DB_USER');
$password   = getenv('DB_PASS');
$dbname     = getenv('DB_NAME');

// Connect DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>