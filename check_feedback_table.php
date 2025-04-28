<?php
include 'connect.php';

// Check if feedback table exists
$result = $conn->query("SHOW TABLES LIKE 'feedback'");
if ($result->num_rows == 0) {
    // Create feedback table with exact specifications
    $sql = "CREATE TABLE feedback (
        id INT(11) NOT NULL AUTO_INCREMENT,
        customer_id INT(11) NOT NULL,
        rating INT(11) NOT NULL,
        comment TEXT,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY customer_id (customer_id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback table created successfully with exact specifications";
    } else {
        echo "Error creating feedback table: " . $conn->error;
    }
} else {
    // Verify table structure
    $result = $conn->query("DESCRIBE feedback");
    if ($result) {
        echo "<h2>Current Feedback Table Structure</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $expected_structure = [
            'id' => ['Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => null, 'Extra' => 'auto_increment'],
            'customer_id' => ['Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'MUL', 'Default' => null, 'Extra' => ''],
            'rating' => ['Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => null, 'Extra' => ''],
            'comment' => ['Type' => 'text', 'Null' => 'YES', 'Key' => '', 'Default' => null, 'Extra' => ''],
            'created_at' => ['Type' => 'timestamp', 'Null' => 'NO', 'Key' => '', 'Default' => 'current_timestamp()', 'Extra' => '']
        ];

        $mismatches = [];
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";

            // Check for mismatches
            if (isset($expected_structure[$row['Field']])) {
                $expected = $expected_structure[$row['Field']];
                foreach ($expected as $key => $value) {
                    if ($row[$key] != $value) {
                        $mismatches[] = "Field '{$row['Field']}' has incorrect {$key}: Expected '{$value}', Found '{$row[$key]}'";
                    }
                }
            }
        }
        echo "</table>";

        if (empty($mismatches)) {
            echo "<p style='color: green;'>Table structure matches exactly!</p>";
        } else {
            echo "<h3>Mismatches Found:</h3>";
            echo "<ul>";
            foreach ($mismatches as $mismatch) {
                echo "<li style='color: red;'>" . $mismatch . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "Error describing feedback table: " . $conn->error;
    }
}

$conn->close();
?> 