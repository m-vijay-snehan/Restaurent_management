<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to return JSON
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pro_db";

try {
    // Create connection to pro_db
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if menu_items table exists
    $checkTable = "SHOW TABLES LIKE 'menu_items'";
    $tableExists = $conn->query($checkTable)->rowCount() > 0;

    if (!$tableExists) {
        throw new Exception("Menu items table does not exist in pro_db. Please run setup_database.sql first.");
    }

    // Query to get all menu items with category
    $sql = "SELECT id, name, description, price, category FROM menu_items ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Fetch all menu items
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug information
    $debug_info = [
        'table_exists' => $tableExists,
        'items_count' => count($menu_items),
        'query' => $sql,
        'database' => $dbname
    ];
    
    // Return success response with menu items and debug info
    echo json_encode([
        'success' => true,
        'data' => $menu_items,
        'debug' => $debug_info
    ]);
    
} catch(PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 