<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'connect.php';

try {
    // Check if menu_items table exists
    $checkTable = "SHOW TABLES LIKE 'menu_items'";
    $tableExists = $conn->query($checkTable)->rowCount() > 0;

    if (!$tableExists) {
        // Create menu_items table
        $sql = "CREATE TABLE menu_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $conn->exec($sql);
        echo "Menu items table created successfully.<br>";

        // Insert sample data
        $sampleData = [
            ['Classic Burger', 399, 'Juicy beef patty with fresh vegetables'],
            ['Margherita Pizza', 499, 'Classic tomato and mozzarella pizza'],
            ['Spaghetti Carbonara', 449, 'Creamy pasta with bacon and cheese'],
            ['Caesar Salad', 299, 'Fresh romaine lettuce with Caesar dressing'],
            ['Chocolate Lava Cake', 349, 'Warm chocolate cake with molten center'],
            ['Signature Cocktails', 399, 'House special mixed drinks']
        ];

        $insertSql = "INSERT INTO menu_items (name, price, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertSql);

        foreach ($sampleData as $item) {
            $stmt->execute($item);
        }
        echo "Sample menu items added successfully.";
    } else {
        echo "Menu items table already exists.";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 