<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

try {
    $sql = "SELECT id, table_number, capacity, status FROM tables WHERE status = 'Available'";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception('Error fetching tables: ' . $conn->error);
    }
    
    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $tables[] = [
            'id' => $row['id'],
            'table_number' => $row['table_number'],
            'capacity' => $row['capacity'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode($tables);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?> 