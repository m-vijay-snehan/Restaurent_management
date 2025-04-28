<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['table_number']) || !isset($_POST['capacity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$table_number = $_POST['table_number'];
$capacity = intval($_POST['capacity']);

// Validate input
if (empty($table_number) || $capacity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Table number and capacity are required, and capacity must be greater than 0'
    ]);
    exit;
}

try {
    // Check if table number already exists
    $check_sql = "SELECT id FROM tables WHERE table_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if (!$check_stmt) {
        throw new Exception('Error preparing check statement: ' . $conn->error);
    }
    
    $check_stmt->bind_param("s", $table_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Table number already exists'
        ]);
        exit;
    }
    
    $check_stmt->close();

    // Insert new table
    $sql = "INSERT INTO tables (table_number, capacity, status) VALUES (?, ?, 'Available')";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("si", $table_number, $capacity);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Table added successfully!',
            'table_id' => $stmt->insert_id
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
