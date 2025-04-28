<?php
include 'connect.php';

try {
    // Create reservations table
    $sql = "CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        table_id INT NOT NULL,
        reservation_time DATETIME NOT NULL,
        party_size INT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (table_id) REFERENCES tables(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Reservations table created successfully<br>";
    } else {
        throw new Exception("Error creating reservations table: " . $conn->error);
    }

    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        table_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (table_id) REFERENCES tables(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Orders table created successfully<br>";
    } else {
        throw new Exception("Error creating orders table: " . $conn->error);
    }

    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        menu_item_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Order items table created successfully<br>";
    } else {
        throw new Exception("Error creating order_items table: " . $conn->error);
    }

    // Create payments table
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(20) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Payments table created successfully<br>";
    } else {
        throw new Exception("Error creating payments table: " . $conn->error);
    }

    // Create feedback table
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        order_id INT NOT NULL,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback table created successfully<br>";
    } else {
        throw new Exception("Error creating feedback table: " . $conn->error);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?> 