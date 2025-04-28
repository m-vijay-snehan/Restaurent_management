<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['order_id']) || !isset($_POST['amount']) || !isset($_POST['payment_method'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$order_id = intval($_POST['order_id']);
$amount = floatval($_POST['amount']);
$payment_method = $_POST['payment_method'];

// Validate input
if ($order_id <= 0 || $amount <= 0 || empty($payment_method)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input values'
    ]);
    exit;
}

try {
    // Check if order exists
    $check_order_sql = "SELECT id FROM orders WHERE id = ?";
    $check_order_stmt = $conn->prepare($check_order_sql);
    $check_order_stmt->bind_param("i", $order_id);
    $check_order_stmt->execute();
    if ($check_order_stmt->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Order does not exist'
        ]);
        exit;
    }
    $check_order_stmt->close();

    // Insert new payment
    $sql = "INSERT INTO payments (order_id, amount, payment_method) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("ids", $order_id, $amount, $payment_method);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment added successfully!',
            'payment_id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception('Error executing statement: ' . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>
