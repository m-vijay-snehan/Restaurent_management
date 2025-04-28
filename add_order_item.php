<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['order_id']) || !isset($_POST['menu_item_id']) || !isset($_POST['quantity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$order_id = $_POST['order_id'];
$menu_item_id = $_POST['menu_item_id'];
$quantity = intval($_POST['quantity']);

// Validate input
if (empty($order_id) || empty($menu_item_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

if ($quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quantity must be greater than 0'
    ]);
    exit;
}

try {
    // Insert new order item
    $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("iii", $order_id, $menu_item_id, $quantity);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Order item added successfully!',
            'order_item_id' => $stmt->insert_id
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
