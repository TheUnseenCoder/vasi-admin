<?php
include '../../../conn.php';

$id = $_GET['id'];

// Select the category name from the admin_categories table based on category_id
$sqlSelectCategoryName = "SELECT category_name FROM admin_categories WHERE category_id = ?";
$stmtSelectCategoryName = $conn->prepare($sqlSelectCategoryName);
$stmtSelectCategoryName->bind_param("i", $id); // "i" specifies that $id is an integer

$stmtSelectCategoryName->execute();
$stmtSelectCategoryName->bind_result($categoryToDelete);
$stmtSelectCategoryName->fetch();
$stmtSelectCategoryName->close();

// Delete the category from the admin_categories table
$sqlDeleteCategory = "DELETE FROM admin_categories WHERE category_id = ?";
$stmtDeleteCategory = $conn->prepare($sqlDeleteCategory);
$stmtDeleteCategory->bind_param("i", $id); // "i" specifies that $id is an integer

if ($stmtDeleteCategory->execute()) {
    // Successful deletion of category from admin_categories table

    // Close the statement
    $stmtDeleteCategory->close();

    // Replace spaces with underscores and convert to lowercase
    $categoryToDelete = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $categoryToDelete));
    
    // Now, let's delete the corresponding column from the admin_records table
    $sqlAlterTable = "ALTER TABLE admin_records DROP COLUMN $categoryToDelete";

    if ($conn->query($sqlAlterTable) === TRUE) {
        // Redirect the user to the previous page after successful deletion
        header('Location: ../category.php');
    } else {
        echo "Error deleting column: " . $conn->error;
    }
} else {
    echo "Error deleting category: " . $stmtDeleteCategory->error;
}

// Close connection
$conn->close();
?>
