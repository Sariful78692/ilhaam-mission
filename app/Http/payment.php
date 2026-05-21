<?php

declare(strict_types=1);

// Razorpay + MySQL payment endpoints.
error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once dirname(__DIR__, 2) . '/config.php';

$keyId = getenv('RAZORPAY_KEY_ID') ?: '';
$keySecret = getenv('RAZORPAY_KEY_SECRET') ?: '';

if ($keyId === '' || $keySecret === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Payment gateway is not configured']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $_GET['action'] ?? 'create-order';

if ($action === 'create-order') {
    $amount = ($input['amount'] ?? 10000);

    $data = [
        'amount' => $amount,
        'currency' => 'INR',
        'receipt' => 'rcpt_' . time(),
        'payment_capture' => 1
    ];

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(500);
        echo json_encode(['error' => 'cURL Error: ' . $error]);
        exit;
    }

    if ($httpCode === 200 || $httpCode === 201) {
        $orderData = json_decode($response, true);
        if ($orderData && isset($orderData['id'])) {
            $order_id = $orderData['id'];
            $stmt = $conn->prepare("INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'created')");
            $stmt->bind_param("si", $order_id, $amount);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                'order_id' => $order_id,
                'key_id' => $keyId,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Invalid Razorpay response']);
        }
    } else {
        http_response_code($httpCode);
        echo json_encode(['error' => 'Razorpay API Error: ' . $response]);
    }

} elseif ($action === 'verify-payment') {
    $razorpay_order_id = $input['razorpay_order_id'] ?? '';
    $razorpay_payment_id = $input['razorpay_payment_id'] ?? '';
    $razorpay_signature = $input['razorpay_signature'] ?? '';

    if (empty($razorpay_order_id) || empty($razorpay_payment_id) || empty($razorpay_signature)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing payment details']);
        exit;
    }

    $generated_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, $keySecret);

    if (hash_equals($generated_signature, $razorpay_signature)) {
        $stmt = $conn->prepare("UPDATE payments SET payment_id=?, status='success' WHERE order_id=?");
        $stmt->bind_param("ss", $razorpay_payment_id, $razorpay_order_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Payment verified']);
    } else {
        $stmt = $conn->prepare("UPDATE payments SET status='failed' WHERE order_id=?");
        $stmt->bind_param("s", $razorpay_order_id);
        $stmt->execute();
        $stmt->close();

        http_response_code(400);
        echo json_encode(['status' => 'failure', 'message' => 'Invalid signature']);
    }

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}

$conn->close();
?>
