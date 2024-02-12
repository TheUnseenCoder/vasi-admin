<?php
include '../../conn.php';

if (isset($_POST['product_name'])) {
    $selectedProductName = $_POST['product_name'];

    // Fetch product details based on the selected product_name
    $sql = "SELECT product_id, status, last_updated FROM ims_products WHERE product_id is not NULL and product_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedProductName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="selected_products[]" class="selectCheckbox" value="' . $row['product_id'] . '"></td>';
            echo '<td class="text-center">' . $row['product_id'] . '</td>';
            
            $formattedDate = date("m-d-Y" . "\n" . "g:ia", strtotime($row['last_updated']));
            echo '<td class="text-center">' . $row['status'] . '</td>';
            echo '<td class="text-center">' . $formattedDate . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4" class="text-center">Out of Stock</td></tr>';
    }

    $stmt->close();
    mysqli_close($conn);
} else {
    echo '<tr><td colspan="4"  class="text-center">Invalid request</td></tr>';
}
?>
