<?php
include 'connect.php';

// Check if feedback table exists
$result = $conn->query("SHOW TABLES LIKE 'feedback'");
if ($result->num_rows == 0) {
    // Create feedback table if it doesn't exist
    $sql = "CREATE TABLE feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback table created successfully";
    } else {
        echo "Error creating feedback table: " . $conn->error;
    }
} else {
    // Display table structure
    $result = $conn->query("DESCRIBE feedback");
    if ($result) {
        echo "<h2>Feedback Table Structure</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error describing feedback table: " . $conn->error;
    }
}

$conn->close();
?> 