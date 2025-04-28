<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['customer_id']) || !isset($_POST['table_id']) || !isset($_POST['reservation_time']) || !isset($_POST['guests'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$customer_id = intval($_POST['customer_id']);
$table_id = intval($_POST['table_id']);
$reservation_time = $_POST['reservation_time'];
$guests = intval($_POST['guests']);

// Validate input
if ($customer_id <= 0 || $table_id <= 0 || empty($reservation_time) || $guests <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required and must be valid'
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

    // Check if table exists
    $check_table_sql = "SELECT id, capacity FROM tables WHERE id = ?";
    $check_table_stmt = $conn->prepare($check_table_sql);
    $check_table_stmt->bind_param("i", $table_id);
    $check_table_stmt->execute();
    $table_result = $check_table_stmt->get_result();
    if ($table_result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Table does not exist'
        ]);
        exit;
    }
    $table = $table_result->fetch_assoc();
    if ($guests > $table['capacity']) {
        echo json_encode([
            'success' => false,
            'message' => 'Number of guests exceeds table capacity'
        ]);
        exit;
    }
    $check_table_stmt->close();

    // Check if table is available at the requested time
    $check_availability_sql = "SELECT id FROM reservations WHERE table_id = ? AND reservation_time = ?";
    $check_availability_stmt = $conn->prepare($check_availability_sql);
    $check_availability_stmt->bind_param("is", $table_id, $reservation_time);
    $check_availability_stmt->execute();
    if ($check_availability_stmt->get_result()->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Table is already reserved for this time'
        ]);
        exit;
    }
    $check_availability_stmt->close();

    // Insert new reservation
    $sql = "INSERT INTO reservations (customer_id, table_id, reservation_time, guests) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param("iisi", $customer_id, $table_id, $reservation_time, $guests);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Reservation added successfully!',
            'reservation_id' => $stmt->insert_id
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
