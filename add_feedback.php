<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['customer_id']) || !isset($_POST['rating']) || !isset($_POST['comment'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$customer_id = intval($_POST['customer_id']);
$rating = intval($_POST['rating']);
$comment = $_POST['comment'];

// Validate input
if ($customer_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input values'
    ]);
    exit;
}

try {
    // Check if customer exists
    $check_customer_sql = "SELECT id FROM customers WHERE id = ?";
    $check_customer_stmt = $conn->prepare($check_customer_sql);
    $check_customer_stmt->bind_param("i", $customer_id);
    $check_customer_stmt->execute();
    if ($check_customer_stmt->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer does not exist'
        ]);
        exit;
    }
    $check_customer_stmt->close();

    // Insert new feedback
    $sql = "INSERT INTO feedback (customer_id, rating, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("iis", $customer_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Feedback added successfully!',
            'feedback_id' => $stmt->insert_id
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
