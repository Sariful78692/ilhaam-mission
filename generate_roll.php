<?php
// generate_roll.php
include 'config.php';
$year = date('Y');
$prefix = "{$year}ILH";
$sql = "SELECT roll_number FROM admissions WHERE roll_number LIKE '$prefix%' ORDER BY roll_number DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
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
$conn->close();
?>