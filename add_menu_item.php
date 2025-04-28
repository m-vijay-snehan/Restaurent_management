<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['category'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$name = $_POST['name'];
$description = $_POST['description'];
$price = floatval($_POST['price']);
$category = $_POST['category'];

// Validate input
if (empty($name) || empty($description) || empty($category) || $price <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required and price must be greater than 0'
    ]);
    exit;
}

try {
    // Check if menu item name already exists
    $check_sql = "SELECT id FROM menu_items WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if (!$check_stmt) {
        throw new Exception('Error preparing check statement: ' . $conn->error);
    }
    
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Menu item name already exists'
        ]);
        exit;
    }
    
    $check_stmt->close();

    // Insert new menu item
    $sql = "INSERT INTO menu_items (name, description, price, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("ssds", $name, $description, $price, $category);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Menu item added successfully!',
            'menu_item_id' => $stmt->insert_id
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
