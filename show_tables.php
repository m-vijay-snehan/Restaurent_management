<?php
include 'connect.php';

$result = $conn->query("SELECT * FROM tables");
if ($result) {
    echo "<h2>Available Tables</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Table Number</th><th>Capacity</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['table_number'] . "</td>";
        echo "<td>" . $row['capacity'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?> 