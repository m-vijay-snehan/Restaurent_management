<?php
include 'connect.php';

$sql = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Feedback table created successfully or already exists";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 