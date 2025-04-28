<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to JSON
header('Content-Type: application/json');

include 'connect.php';

// Check if all required fields are present
if (!isset($_POST['customer_id']) || !isset($_POST['table_id']) || !isset($_POST['items'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$customer_id = intval($_POST['customer_id']);
$table_id = intval($_POST['table_id']);
$items = json_decode($_POST['items'], true);

// Validate input
if ($customer_id <= 0 || $table_id <= 0 || !is_array($items) || empty($items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input values'
    ]);
    exit;
}

try {
    // Check if customer exists
    $check_customer_sql = "SELECT id FROM customers WHERE id = ?";
    $check_customer_stmt = $conn->prepare($check_customer_sql);
    $check_customer_stmt->bind_param("i", $customer_id);
    $check_customer_stmt->execute();
    if ($check_customer_stmt->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer does not exist'
        ]);
        exit;
    }
    $check_customer_stmt->close();

    // Check if table exists
    $check_table_sql = "SELECT id FROM tables WHERE id = ?";
    $check_table_stmt = $conn->prepare($check_table_sql);
    $check_table_stmt->bind_param("i", $table_id);
    $check_table_stmt->execute();
    if ($check_table_stmt->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Table does not exist'
        ]);
        exit;
    }
    $check_table_stmt->close();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert order
        $order_sql = "INSERT INTO orders (customer_id, table_id, status) VALUES (?, ?, 'Pending')";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("ii", $customer_id, $table_id);
        $order_stmt->execute();
        $order_id = $order_stmt->insert_id;
        $order_stmt->close();

        // Insert order items
        foreach ($items as $item) {
            $menu_item_id = intval($item['menu_item_id']);
            $quantity = intval($item['quantity']);
            
            if ($menu_item_id <= 0 || $quantity <= 0) {
                throw new Exception('Invalid menu item or quantity');
            }

            // Check if menu item exists
            $check_menu_sql = "SELECT id FROM menu_items WHERE id = ?";
            $check_menu_stmt = $conn->prepare($check_menu_sql);
            $check_menu_stmt->bind_param("i", $menu_item_id);
            $check_menu_stmt->execute();
            if ($check_menu_stmt->get_result()->num_rows === 0) {
                throw new Exception('Menu item not found');
            }
            $check_menu_stmt->close();

            $order_item_sql = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)";
            $order_item_stmt = $conn->prepare($order_item_sql);
            $order_item_stmt->bind_param("iii", $order_id, $menu_item_id, $quantity);
            $order_item_stmt->execute();
            $order_item_stmt->close();
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $order_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>
