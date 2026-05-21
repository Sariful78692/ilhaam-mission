<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config.php';

$year = date('Y');
$prefix = "{$year}ILH";

$stmt = $conn->prepare('SELECT roll_number FROM admissions WHERE roll_number LIKE ? ORDER BY roll_number DESC LIMIT 1');
$likePrefix = $prefix . '%';
$stmt->bind_param('s', $likePrefix);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_roll = $row['roll_number'];
    $last_number = (int)substr($last_roll, strlen($prefix));
    $next_number = $last_number + 1;
} else {
    $next_number = 1;
}
$roll_number = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
header('Content-Type: application/json');
echo json_encode(['roll_number' => $roll_number]);
$stmt->close();
$conn->close();
