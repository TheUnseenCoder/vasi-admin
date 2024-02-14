<?php
include '../../../conn.php';
date_default_timezone_set('Asia/Manila');

if (isset($_POST["update_employee"])) {
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];

    $sql = "UPDATE admin_employee SET employee_name = ? WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $employee_name, $employee_id);

    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($conn);
        header('Location: ../employees.php');
    }
}
elseif (isset($_POST["update_category"])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $category_old_name = strtolower($_POST['category_old_name']);
    $category_new_name = strtolower($_POST['category_name']);
    
    $sqlUpdateCategory = "UPDATE admin_categories SET category_name = ? WHERE category_id = ?";
    $stmtUpdateCategory = $conn->prepare($sqlUpdateCategory);
    $stmtUpdateCategory->bind_param("si", $category_name, $category_id);
    
    if ($stmtUpdateCategory->execute()) {
        $stmtUpdateCategory->close();
        $category_old_name = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_old_name));
        $category_new_name = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_new_name));
    
        $sqlAlterTable = "ALTER TABLE admin_records CHANGE $category_old_name $category_new_name DOUBLE NULL DEFAULT 0";    
        if ($conn->query($sqlAlterTable) === TRUE) {
            header('Location: ../category.php');
        } else {
            echo "Error altering table: " . $conn->error;
        }
    } else {
        echo "Error updating category: " . $stmtUpdateCategory->error;
    }
    
    $conn->close();
}
elseif (isset($_POST["update_warehouse"])) {
    $warehouse_id = $_POST['warehouse_id'];
    $warehouse_name = $_POST['warehouse_name'];
    $warehouse_add = $_POST['warehouse_add'];

    $sql = "UPDATE admin_warehouses SET warehouse_name = ?, warehouse_add = ? WHERE warehouse_id = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssi", $warehouse_name, $warehouse_add, $warehouse_id);

    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($conn);
        header('Location: ../warehouse.php');
    }
}
elseif (isset($_POST["update_supplier"])) {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $supplier_add = $_POST['supplier_add'];
    $supplier_email = $_POST['supplier_email'];

    $sql = "UPDATE admin_suppliers SET supplier_name = ?, supplier_add = ?, supplier_email = ? WHERE supplier_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $supplier_name, $supplier_add, $supplier_email, $supplier_id);
    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($conn);
        header('Location: ../supplier.php');
    }
}
elseif (isset($_POST["update_record"])) {
     $id = $_POST['id'];
     $requested_by = $_POST['requested_by'];
     $project_site = $_POST['project_site'];
     $purpose = $_POST['purpose'];
     $amount = $_POST['amount'];
     $returned_cash = $_POST['returned_cash'];
     $category_name = $_POST['category_name'];
     $category_amount = $_POST['category_amount'];
     $old_category_name = $_POST['old_category'];
 
     $sql = "UPDATE admin_records SET requested_by = '$requested_by', project_site = '$project_site', purpose = '$purpose', amount = '$amount', returned_cash = '$returned_cash', $category_name = '$category_amount', $old_category_name = 0 WHERE record_id = '$id'"; 
     if (mysqli_query($conn, $sql)) {
        header("Location: ../recents.php");
     } else {
         echo "Error adding column: " . $conn->error;
     }

     mysqli_close($conn);
} 
elseif (isset($_POST["update_account"])) {
    // WAG GAGALAWIN!!!
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $privilege = "Administrator";
    $old_profile_picture = $_POST['old_profile_image'];
    $old_username = trim($_POST['old_username']);
    $old_password = trim($_POST['old_password']);
    $password_hashed = $old_password_hashed = null;
    if (!empty($password)) {
        $password_hashed = md5($password);
    }
    $uploadDirectory = 'uploads/';
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES['profile_image']['tmp_name'];
        $image_name = $_FILES['profile_image']['name'];
        $new_image_path = $uploadDirectory . $image_name;
        move_uploaded_file($temp_name, $new_image_path);
    } else {
        $new_image_path = $old_profile_picture;
    }
    $changed_fields = [];
    if ($username != $old_username) {
        $changed_fields['username'] = $username;
    }
    if (!empty($password) && $password_hashed != $old_password_hashed) {
        $changed_fields['password'] = $password_hashed;
    }
    if ($new_image_path != $old_profile_picture) {
        $changed_fields['profile'] = $new_image_path;
    }
   if (!empty($changed_fields)) {
        $sql = "UPDATE admin_login SET ";
        $types = '';
        foreach ($changed_fields as $field => $value) {
            $sql .= "$field = ?, ";
            $types .= 's'; 
        }
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE privilege = ?";
        $types .= 's';
        $stmt = $conn->prepare($sql);
        $values = array_values($changed_fields);
        $values[] = $privilege; 
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: ../dashboard.php');
        }
    }
}
elseif (isset($_POST["update_design"])) {
    $title = trim($_POST['title']);
    $old_title = trim($_POST['old_title']);
    $old_logo_default = str_replace('functions/', '', $_POST['old_logo_default']);

    $uploadDirectory = 'uploads/';
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    if (isset($_FILES['profile_image1']) && $_FILES['profile_image1']['error'] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES['profile_image1']['tmp_name'];
        $image_name = $_FILES['profile_image1']['name'];
        $new_image_path = $uploadDirectory . $image_name;
        move_uploaded_file($temp_name, $new_image_path);
    } else {
        $new_image_path = $old_logo_default;
    }

    if ($title !== $old_title || $new_image_path !== $old_logo_default) {
        $sql = "UPDATE admin_designs SET logo = ?, title = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_image_path, $title);

        if ($stmt->execute()) {
            $stmt->close();
            mysqli_close($conn);
            header('Location: ../dashboard.php');
        }
    }
}
?>
