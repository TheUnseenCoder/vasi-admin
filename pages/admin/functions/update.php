<?php
session_start();
include '../../../conn.php';
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);


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
    $category_newest = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_new_name));
    $sqlUpdateCategory = "UPDATE admin_categories SET category_name = ?, category_change =? WHERE category_id = ?";
    $stmtUpdateCategory = $conn->prepare($sqlUpdateCategory);
    $stmtUpdateCategory->bind_param("ssi", $category_name, $category_newest, $category_id);
    
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
elseif (isset($_POST["update_record"])) {
    // Retrieve form data
    $id = $_POST['id'];
    $requested_by = $_POST['requested_by'];
    $project_site = $_POST['project_site'];
    $purpose = $_POST['purpose'];
    $amounts = $_POST['amount'];
    $returned_cash = $_POST['returned_cash'];

    // Prepare arrays for expenses data
    $transaction_date = $_POST['update_transaction_date'];
    $v_nv = $_POST['update_v_nv'];
    $update_supplier_name = $_POST['update_supplier_name'];
    $update_address = isset($_POST['update_address']) ? $_POST['update_address'] : "No Address";
    $update_category_name = $_POST['update_category_name'];
    $update_category_amount = $_POST['update_category_amount'];
    $update_tin = isset($_POST['update_tin']) ? $_POST['update_tin'] : "No TIN";
    $update_doc_type = $_POST['update_doc_type'];
    $update_doc_num =  isset($_POST['update_doc_num']) ? $_POST['update_doc_num'] : "No Doc Num";
    $update_goods_service_others = $_POST['update_goods_service_others'];
    $update_particular = $_POST['update_particular'];
    $old_category_name = $_POST['old_category_name'];

    // Compute total amounts
    $totalAmount = $amounts + $returned_cash;

    // Iterate over each category amount and sum them up
    $totalCategoryAmount = 0;
    foreach ($_POST['update_category_amount'] as $amountString) {
        $individualAmounts = explode(', ', $amountString);
        foreach ($individualAmounts as $amount) {
            $totalCategoryAmount += floatval($amount);
        }
    }

    if ($totalAmount != $totalCategoryAmount) {
        // Return error message
        $response = array("failed" => true, "message" => "The total amount entered in the categories does not match the total amount.");
        $_SESSION['response'] = $response;
    } else {
        // Prepare values for SQL query
        $category_amount = implode(", ", $update_category_amount);
        $category_name = implode(", ", $update_category_name);
        $supplier_names_str = implode(", ", $update_supplier_name);
        $addresses_str = implode(", ", $update_address);
        $tins_str = implode(", ", $update_tin);
        $doc_types_str = implode(", ", $update_doc_type);
        $doc_nums_str = implode(", ", $update_doc_num);
        $goods_service_others_str = implode(", ", $update_goods_service_others);
        $particulars_str = implode(", ", $update_particular);
        $transaction_date_str = implode(", ", $transaction_date);
        $v_nv_str = implode(", ", $v_nv);
       
        // Determine company types for each category name
        $company_types = [];
        foreach ($update_supplier_name as $name) {
            $company_type = "";
            $name_lower = strtolower($name);
            if (
                strpos($name_lower, "corp") !== false || 
                strpos($name_lower, "inc") !== false || 
                strpos($name_lower, "corporation") !== false || 
                strpos($name_lower, "company") !== false || 
                strpos($name_lower, "incorporated") !== false
            ) {
                $company_type = "Corporation";
            } elseif (
                substr($name_lower, -2) === "co" || 
                substr($name_lower, -3) === "co." || 
                substr($name_lower, -3) === "ltd" || 
                substr($name_lower, -5) === "hotel"
            ) {
                $company_type = "Corporation";
            } else {
                $company_type = "Individual";
            }
            $company_types[] = $company_type;
        }
        $company_types_str = implode(", ", $company_types);


        // Construct the SQL query for updating the main record
        $sqlMainRecord = "UPDATE admin_records SET requested_by = '$requested_by', project_site = '$project_site', purpose = '$purpose', amount = '$amounts', returned_cash = '$returned_cash'";
        foreach ($old_category_name as $value) {
            $sqlMainRecord .= ", `$value` = 0";
        }
        foreach ($update_category_name as $key => $value) {
            $sqlMainRecord .= ", `$value` = '{$update_category_amount[$key]}'";
        }
        $sqlMainRecord .= " WHERE record_id = '$id'";
    
        // Execute the main record update query
        if (mysqli_query($conn, $sqlMainRecord)) {
            // Construct the SQL query for updating expenses details
            $sqlUpdateExpense = "UPDATE admin_record_details SET transaction_date = '$transaction_date_str', category_names = '$category_name', category_amounts = '$category_amount', supplier_name = '$supplier_names_str', address = '$addresses_str', tin = '$tins_str', doc_type = '$doc_types_str', doc_num = '$doc_nums_str', goods_service_others = '$goods_service_others_str', particulars = '$particulars_str', company_types = '$company_types_str', v_nv = '$v_nv_str' WHERE record_id = '$id'";
            // Execute the expenses details update query
            if (mysqli_query($conn, $sqlUpdateExpense)) {
                $response = array("success" => true, "message" => "Record updated successfully.");
            } else {
                $response = array("failed" => true, "message" => "Error updating expenses details: " . mysqli_error($conn));
            }
        } else {
            $response = array("failed" => true, "message" => "Error updating record: " . mysqli_error($conn));
        }
    
        // Store response in session
        $_SESSION['response'] = $response;
    }

    // Redirect to the appropriate page
    header('Location: ../recents.php?modal-update=' . $id);
    exit();
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
elseif(isset($_POST['update_user'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $privilege = $_POST['privilege'];
    $status = $_POST['status'];
    $pass = md5($password);

    $selector = "SELECT * FROM admin_login WHERE id = '$id' and password != '$pass'";
    $result = mysqli_query($conn, $selector);
    if(mysqli_num_rows($result) > 0){
        $update = "UPDATE admin_login SET username='$username', full_name='$full_name', password='$pass', privilege='$privilege', status='$status' WHERE id='$id'";
        if(mysqli_query($conn, $update)){
            header('Location: ../userlist.php');   
        }
        else{
            echo "Error: " . $conn->error;
        }
    }
    else{
        $update = "UPDATE admin_login SET username='$username', full_name='$full_name', privilege='$privilege', status='$status' WHERE id='$id'";
        if(mysqli_query($conn, $update)){
            header('Location: ../userlist.php');   
        }
        else{
            echo "Error: " . $conn->error;
        }
    }
} else {
    // If update_record parameter is not set, return an error
    $response = array("success" => false, "message" => "Invalid request.");
    echo json_encode($response);
}
?>
