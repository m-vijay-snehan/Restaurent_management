<?php
include 'connect.php';

// Function to execute SQL queries
function executeQuery($conn, $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Query executed successfully<br>";
        return true;
    } else {
        echo "Error executing query: " . $conn->error . "<br>";
        return false;
    }
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS restaurant_management";
executeQuery($conn, $sql);

// Select database
$conn->select_db("restaurant_management");

// Drop existing tables in correct order
$tables = [
    'order_items',
    'payments',
    'orders',
    'reservations',
    'feedback',
    'menu_items',
    'tables',
    'staff',
    'customers'
];

foreach ($tables as $table) {
    $sql = "DROP TABLE IF EXISTS $table";
    executeQuery($conn, $sql);
}

// Create customers table
$sql = "CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql);

// Create staff table
$sql = "CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql);

// Create tables table
$sql = "CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(20) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql);

// Create menu_items table
$sql = "CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql);

// Create orders table
$sql = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    table_id INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (table_id) REFERENCES tables(id)
)";
executeQuery($conn, $sql);

// Create order_items table
$sql = "CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
)";
executeQuery($conn, $sql);

// Create payments table
$sql = "CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";
executeQuery($conn, $sql);

// Create reservations table
$sql = "CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    table_id INT NOT NULL,
    reservation_time DATETIME NOT NULL,
    guests INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (table_id) REFERENCES tables(id)
)";
executeQuery($conn, $sql);

// Create feedback table
$sql = "CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";
executeQuery($conn, $sql);

// Insert sample data
$sample_data = [
    // Customers
    "INSERT INTO customers (name, phone, email, address) VALUES 
    ('John Doe', '1234567890', 'john@example.com', '123 Main St'),
    ('Jane Smith', '0987654321', 'jane@example.com', '456 Oak Ave')",
    
    // Staff
    "INSERT INTO staff (name, position, phone, salary) VALUES 
    ('Mike Johnson', 'Manager', '1112223333', 5000.00),
    ('Sarah Williams', 'Waiter', '4445556666', 2500.00)",
    
    // Tables
    "INSERT INTO tables (table_number, capacity) VALUES 
    ('T1', 4),
    ('T2', 6),
    ('T3', 2),
    ('T4', 8)",
    
    // Menu Items
    "INSERT INTO menu_items (name, description, price, category) VALUES 
    ('Margherita Pizza', 'Classic tomato and mozzarella', 499.00, 'Main Course'),
    ('Caesar Salad', 'Fresh romaine with parmesan', 299.00, 'Starter'),
    ('Chocolate Cake', 'Rich chocolate dessert', 199.00, 'Dessert')"
];

foreach ($sample_data as $sql) {
    executeQuery($conn, $sql);
}

echo "<h2>Database setup completed!</h2>";
$conn->close();
?> 