<?php
// Include your database connection code
include '../../../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $product_id = $_POST['product_id'];
    $new_status = $_POST['status'];

    // Retrieve the product name
    $sqlGetProductName = "SELECT product_name FROM admin_products WHERE product_id = ?";
    $stmtGetProductName = $conn->prepare($sqlGetProductName);
    $stmtGetProductName->bind_param("i", $product_id);
    $stmtGetProductName->execute();
    $resultGetProductName = $stmtGetProductName->get_result();

    if ($resultGetProductName->num_rows > 0) {
        $rowGetProductName = $resultGetProductName->fetch_assoc();
        $product_name = $rowGetProductName['product_name'];

        // Update the status in the database
        $sqlUpdateStatus = "UPDATE admin_products SET status = ? WHERE product_id = ?";
        $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
        $stmtUpdateStatus->bind_param("si", $new_status, $product_id);

        if ($stmtUpdateStatus->execute()) {
            // Successful update

            // Log the status change in admin_logs
            $logMessage = "Changed status of product {$product_name}({$product_id}) to '{$new_status}'";
            $sqlAddLog = "INSERT INTO admin_logs (subject, description, date_created) VALUES (?, ?, NOW())";
            $subject = "Status Changes";
            $stmtAddLog = $conn->prepare($sqlAddLog);
            $stmtAddLog->bind_param("ss", $subject, $logMessage);
            $stmtAddLog->execute();
            $stmtAddLog->close();

            $stmtUpdateStatus->close();
            $stmtGetProductName->close();
            mysqli_close($conn);
            header('Location: ../stocks.php');
            exit();
        } else {
            // Handle the error
            echo "Error updating status: " . $stmtUpdateStatus->error;
        }
    } else {
        echo "Product not found.";
    }
} else {
    // Handle invalid requests
    echo "Invalid request.";
}
?>
