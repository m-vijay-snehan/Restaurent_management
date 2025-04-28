<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['name']) || !isset($_POST['position']) || !isset($_POST['phone']) || !isset($_POST['salary'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$name = $_POST['name'];
$position = $_POST['position'];
$phone = $_POST['phone'];
$salary = floatval($_POST['salary']);

// Validate input
if (empty($name) || empty($position) || empty($phone) || $salary <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input values'
    ]);
    exit;
}

try {
    // Insert new staff
    $sql = "INSERT INTO staff (name, position, phone, salary) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("sssd", $name, $position, $phone, $salary);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Staff added successfully!',
            'staff_id' => $stmt->insert_id
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
