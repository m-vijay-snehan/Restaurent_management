<?php
include 'connect.php';

// Function to create or verify table structure
function setupFeedbackTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT(11) NOT NULL AUTO_INCREMENT,
        customer_id INT(11) NOT NULL,
        rating INT(11) NOT NULL,
        comment TEXT,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY customer_id (customer_id)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback table structure verified/created successfully<br>";
        return true;
    } else {
        echo "Error with feedback table: " . $conn->error . "<br>";
        return false;
    }
}

// Function to add sample data
function addSampleData($conn) {
    $sample_data = [
        [1, 5, "Excellent service and food quality!"],
        [2, 4, "Good experience overall, but service was a bit slow."],
        [3, 3, "Average experience, food was okay."],
        [4, 5, "Amazing food and great atmosphere!"],
        [5, 2, "Not satisfied with the service."]
    ];

    $stmt = $conn->prepare("INSERT INTO feedback (customer_id, rating, comment) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error . "<br>";
        return;
    }

    $inserted = 0;
    foreach ($sample_data as $data) {
        $stmt->bind_param("iis", $data[0], $data[1], $data[2]);
        if ($stmt->execute()) {
            $inserted++;
        }
    }

    echo "Successfully inserted $inserted sample feedback records<br>";
    $stmt->close();
}

// Function to display current table data
function displayTableData($conn) {
    $result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
    if ($result) {
        echo "<h3>Current Feedback Data:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Customer ID</th><th>Rating</th><th>Comment</th><th>Created At</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['customer_id'] . "</td>";
            echo "<td>" . $row['rating'] . "</td>";
            echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error fetching data: " . $conn->error . "<br>";
    }
}

// Main execution
echo "<h2>Feedback Table Setup</h2>";

if (setupFeedbackTable($conn)) {
    // Add sample data
    addSampleData($conn);
    
    // Display current data
    displayTableData($conn);
}

$conn->close();
?> 