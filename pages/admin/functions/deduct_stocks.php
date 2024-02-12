<?php
// Include your database connection code
include '../../../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_name'], $_POST['selected_products'])) {
        $selectedProductName = $_POST['product_name'];
        $selectedProducts = $_POST['selected_products'];

        // Check if "Select All" is checked
        $selectAll = in_array('selectAll', $selectedProducts);

        // Construct the SQL query
        if ($selectAll) {
            // Delete all records for the selected product_name without considering product_id
            $sql = "DELETE FROM admin_products WHERE product_id is not NULL AND product_name = ?";
            $logMessage = "Deducted all stocks for product '{$selectedProductName}'";
        } else {
            // Delete only the selected product_id records for the given product_name
            $placeholders = implode(',', array_fill(0, count($selectedProducts), '?'));
            $sql = "DELETE FROM admin_products WHERE product_name = ? AND product_id IN ({$placeholders})";
            $logMessage = "Deducted " . count($selectedProducts) . " stock(s) for product '{$selectedProductName}'";
        }

        $stmt = $conn->prepare($sql);

        if ($selectAll) {
            // If "Select All," bind only one parameter
            $stmt->bind_param("s", $selectedProductName);
        } else {
            // If specific products, bind product_name and multiple product_id parameters
            $typeDefinition = "s" . str_repeat('s', count($selectedProducts));
            $params = array_merge([$selectedProductName], $selectedProducts);
            $stmt->bind_param($typeDefinition, ...$params);
        }

        // Execute the query
        $stmt->execute();

        // Log the deduction in admin_logs
        $sqlAddLog = "INSERT INTO admin_logs (subject, description, date_created) VALUES (?, ?, NOW())";
        $subject = "Stock Changes";
        $stmtAddLog = $conn->prepare($sqlAddLog);
        $stmtAddLog->bind_param("ss", $subject, $logMessage);
        $stmtAddLog->execute();
        $stmtAddLog->close();

        $stmt->close();
        mysqli_close($conn);
        header('Location: ../stocks.php');
        exit();
    } else {
        echo "Invalid product selection.";
    }
} else {
    // Handle invalid requests
    echo "Invalid request.";
}
?>
