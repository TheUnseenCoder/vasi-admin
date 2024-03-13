<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include '../../../conn.php';
date_default_timezone_set('Asia/Manila');

// function generateSerialNumber() {
//     // Set the time zone to Asia/Manila
//     date_default_timezone_set('Asia/Manila');
    
//     // Get the current date components
//     $year = date("Y");
//     $month = date("m");
//     $day = date("d");
    
//     // Generate a random 4-digit number
//     $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
//     // Create the serial number
//     $serialNumber = "$year$month$day$randomNumber";
    
//     return $serialNumber;
// }

if (isset($_POST["save_employee"])) {
    // Get the employee name from the form
    $employee_name = $_POST['employee_name'];

    // Create a prepared statement for inserting a employee
    $sql = "INSERT INTO admin_employee (employee_name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute the query
        mysqli_stmt_bind_param($stmt, "s", $employee_name);

        if (mysqli_stmt_execute($stmt)) {
            // Query executed successfully
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: ../employees.php');
        } else {
            // Handle errors here
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Handle errors in preparing the statement
        echo "Error: " . mysqli_error($conn);
    }
} 
elseif (isset($_POST["save_category"])) {
    $category_name = $_POST['category_name'];
    $category_column = strtolower($category_name);

    // Replace spaces with underscores and convert to lowercase
    $category_column = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_column));

    $sqlAddColumn = "ALTER TABLE admin_records ADD COLUMN $category_column DOUBLE NULL DEFAULT 0";

    if ($conn->query($sqlAddColumn) === TRUE) {
        echo "Column added successfully";

        // Now, you can proceed with your existing code for inserting a category

        // Create a prepared statement for inserting a category
        $sqlInsertCategory = "INSERT INTO admin_categories (category_name, category_change) VALUES (?, ?)";
        $stmtInsertCategory = mysqli_prepare($conn, $sqlInsertCategory);

        if ($stmtInsertCategory) {
            // Bind parameters and execute the query
            mysqli_stmt_bind_param($stmtInsertCategory, "ss", $category_name, $category_column);

            if (mysqli_stmt_execute($stmtInsertCategory)) {
                // Query executed successfully
                mysqli_stmt_close($stmtInsertCategory);
                mysqli_close($conn);

                header('Location: ../category.php');
            } else {
                // Handle errors here
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            // Handle errors in preparing the statement
            echo "Error: " . mysqli_error($conn);
        }

    } else {
        echo "Error adding column: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
elseif (isset($_POST["save_record"])) {
    // Extract data from $_POST
    $requested_by = $_POST['add_requested_by'];
    $project_site = $_POST['add_project_site'];
    $purpose = $_POST['add_purpose'];
    $amount = $_POST['add_amount'];
    $returned_cash = $_POST['add_returned_cash'];
    $category_names = $_POST['add_category_name'];
    $category_amounts = $_POST['add_category_amount'];
    $supplier_names = $_POST['add_supplier_name'];
    $addresses = $_POST['add_address'];
    $tins = $_POST['add_tin'];
    $doc_types = $_POST['add_doc_type'];
    $doc_nums = isset($_POST['add_doc_num']) ? $_POST['add_doc_num'] : "No Receipt";
    $goods_service_others = $_POST['add_goods_service_others'];
    $particulars = $_POST['add_particulars'];

    // Convert array values to comma-separated strings
    $category_names_str = implode(", ", array_map(function($name) { return "`$name`"; }, $category_names));
    $category_amounts_str = implode(", ", $category_amounts);
    
    // Combine arrays into single strings
    $category_amount = "'" . implode(", ", $category_amounts) . "'";
    $category_name = "'" . implode(", ", $category_names) . "'";
    $supplier_names_str = "'" . implode(", ", $supplier_names) . "'";
    $addresses_str = "'" . implode(", ", $addresses) . "'";
    $tins_str = "'" . implode(", ", $tins) . "'";
    $doc_types_str = "'" . implode(", ", $doc_types) . "'";
    $doc_nums_str = isset($_POST['add_doc_num']) ? "'" . implode(", ", $_POST['add_doc_num']) . "'" : "'No Receipt'";
    $goods_service_others_str = "'" . implode(", ", $goods_service_others) . "'";
    $particulars_str = "'" . implode(", ", $particulars) . "'";

    // Create SQL query for admin_records table
    $sql_records = "INSERT INTO admin_records (requested_by, project_site, purpose, amount, returned_cash, $category_names_str) 
                    VALUES ('$requested_by', '$project_site', '$purpose', '$amount', '$returned_cash', $category_amounts_str)";

    // Execute SQL query for admin_records table
    if (mysqli_query($conn, $sql_records)) {
        $record_id = mysqli_insert_id($conn);

        // Create SQL query for admin_record_details table
        $sql_details = "INSERT INTO admin_record_details (record_id, category_names, category_amounts, supplier_name, address, tin, doc_type, doc_num, goods_service_others, particulars) 
                        VALUES ('$record_id', $category_name, $category_amount, $supplier_names_str, $addresses_str, $tins_str, $doc_types_str, $doc_nums_str, $goods_service_others_str, $particulars_str)";

        // Execute SQL query for admin_record_details table
        if (mysqli_query($conn, $sql_details)) {
            mysqli_close($conn);
            // ADD A SUCCESS SWEETALERT UPON DEPLOYMENT
            header('Location: ../sample.php'); //change to recents.php when done
            exit();
        } else {
            // ADD A ERROR SWEETALERT UPON DEPLOYMENT
            echo "Error adding record details: " . mysqli_error($conn);
            echo "SQL for admin_records: $sql_records <br>";
            echo "SQL for admin_record_details: $sql_details <br>";

        }
    } else {
        echo "Error adding record: " . mysqli_error($conn);
    }
}
elseif (isset($_POST["save_supplier"])) {
    $supplier_name = $_POST['supplier_name'];
    $supplier_add = $_POST['supplier_add'];
    $supplier_email = $_POST['supplier_email'];

    // Create a prepared statement for inserting a supplier
    $sql = "INSERT INTO admin_suppliers (supplier_name, supplier_add, supplier_email) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute the query
        mysqli_stmt_bind_param($stmt, "sss", $supplier_name, $supplier_add, $supplier_email);

        if (mysqli_stmt_execute($stmt)) {
            // Query executed successfully
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: ../supplier.php');
            exit(); // Terminate the script to prevent further execution
        } else {
            // Handle errors here
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Handle errors in preparing the statement
        echo "Error: " . mysqli_error($conn);
    }
}
elseif (isset($_POST["add_stocks"])) {
    // Get the form data
    $stock = $_POST['stock'];
    $selectedProductName = $_POST['product_name'];
    $manualProductIDs = explode(' ', $_POST['product_id']); // Split product IDs by space

    // Check if the selected product exists
    $sqlCheckProduct = "SELECT * FROM admin_products WHERE product_name = ? AND status = 'Usable'";
    $stmtCheckProduct = $conn->prepare($sqlCheckProduct);
    $stmtCheckProduct->bind_param("s", $selectedProductName);
    $stmtCheckProduct->execute();
    $resultCheckProduct = $stmtCheckProduct->get_result();

    if ($resultCheckProduct->num_rows > 0) {
        // Product exists, proceed to add stocks
        $rowCheckProduct = $resultCheckProduct->fetch_assoc();

        // Insert into admin_products using the manually entered product IDs
        foreach ($manualProductIDs as $manualProductID) {
            // Insert into admin_products
            $sqlAddStocks = "INSERT INTO admin_products (product_id, product_image, product_name, product_description, category, employee, supplier, warehouse, status, last_updated) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmtAddStocks = $conn->prepare($sqlAddStocks);
            $stmtAddStocks->bind_param("sssssssss", $manualProductID, $rowCheckProduct['product_image'], $rowCheckProduct['product_name'], $rowCheckProduct['product_description'],
                $rowCheckProduct['category'], $rowCheckProduct['employee'], $rowCheckProduct['supplier'],
                $rowCheckProduct['warehouse'], $rowCheckProduct['status']);
            $stmtAddStocks->execute();
            $stmtAddStocks->close();
        }

        // Log the stock addition in admin_logs
        $logMessage = "Added " . count($manualProductIDs) . " stock(s) for product '{$rowCheckProduct['product_name']}'";
        $sqlAddLog = "INSERT INTO admin_logs (subject, description, date_created) VALUES (?, ?, NOW())";
        $subject = "Stock Changes";
        $stmtAddLog = $conn->prepare($sqlAddLog);
        $stmtAddLog->bind_param("ss", $subject, $logMessage);
        $stmtAddLog->execute();
        $stmtAddLog->close();

        mysqli_close($conn);
        header('Location: ../stocks.php');
        exit();
    } else {
        echo "Selected product not found or is not usable.";
    }
}
elseif(isset($_POST['save_user'])){
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $privilege = $_POST['privilege'];
    $status = $_POST['status'];

    if ($password == $confirm_password){
        $password = md5($password);

        $selector = "SELECT * FROM admin_login WHERE username = '$username'";
        $result = mysqli_query($conn, $selector);
        if(mysqli_num_rows($result) > 0){
            echo "Username is already taken";
        }
        else{
            $sql = "INSERT admin_login SET full_name='$full_name', username='$username', password='$password', privilege='$privilege', status='status'";
            if(mysqli_query($conn, $sql)){
                header("Location: ../userlist.php");
                exit();
            }
            else{
                echo "Error: " . mysqli_error($conn);
                header("Location: ../userlist.php");
                exit();
            }
        }    
    }

}

?>