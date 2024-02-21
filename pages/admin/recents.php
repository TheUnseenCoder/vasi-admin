<?php

session_start();

include '../../conn.php';

if(isset($_SESSION["loggedinasadmin"]) || isset($_SESSION["loggedinasmainuser"])){

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php include 'components/icon.php'; ?>

  <title><?php echo $title; ?> | Recent Records</title>


<!-- Add SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <!-- Main Template -->
  <link rel="stylesheet" href="../../assets/css/styles.min.css">

</head>

<body>
<?php include '../admin/components/navigation.php'; ?>

  <!--  Main wrapper -->
  <div class="body-wrapper">

    <?php include '../admin/components/header.php'; ?>

      <div class="container-fluid">
        <div class="card w-100">
          <div class="card-body p-4">
            <div class="d-flex">
              <h5 class="card-title fw-semibold mb-4">Employee List
              </h5>
              <div class="flex-grow-1"></div>
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="ti ti-square-plus fs-6"></i>
                Add Record
              </button>
            </div>
            <div class="mb-3">
                <label for="searchInput" class="form-label">Search:</label>
                <input type="text" class="form-control" id="searchInput" onkeyup="searchTable()" placeholder="Enter search terms">
            </div>
            <div class="table-responsive">
              <table id="myTable" class="table text-nowrap mb-0 align-middle" >
                <thead class="text-dark fs-4">
                  <tr>
                  <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">ID</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Requested By</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Purpose</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Project/Site</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Amount</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Returned Cash</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Date Encoded</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Reference#</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Action</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT * FROM admin_records ORDER BY date_encoded DESC";
                    if($rs=$conn->query($sql)){
                        $i = 1;
                        while ($row=$rs->fetch_assoc()) {
                          $date_encoded = $row['date_encoded'];
                          $formatted_date = date("M d, Y g:iA", strtotime($date_encoded));              
                    ?>
                  <tr>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $i++; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['requested_by']; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['purpose']; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['project_site']; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['amount']; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['returned_cash']; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $formatted_date; ?></h6></td>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $row['reference_num']    ?></h6></td>
                    <td class="border-bottom-0 text-center">
                        <a class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#update-modal<?php echo $row['record_id']; ?>"><i class="ti ti-edit fs-3"></i> Update</a>
                        <!--<a href="functions/delete_product.php?id=<?php echo $row['product_name']; ?>" class="btn btn-sm btn-danger"><i class="ti ti-trash fs-3"></i> Delete</a>-->
                    </td>
                    <?php
                            }
                                }
                          ?>
                  </tr>      
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>

<div class="modal fade modal-xl" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add a Record</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md">
            <form id="addProductForm" action="functions/add.php" method="post" enctype="multipart/form-data">

            <div class="row mb-2">
                <div class="col-md-6">
                  <label for="requestedbySelect">Requested By:</label>
                  <select name="requested_by" class="form-select" id="requestedbySelect" required>
                    <option value="0" disabled selected>Select Employee</option>
                    <?php
                    $sql = "SELECT * FROM admin_employee";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                      $employee_name = $row['employee_name']; 

                      echo "<option value=\"$employee_name\">$employee_name</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="categorySelect">Project/Site</label>
                  <input type="textarea" name="project_site" class="form-control" id="project_site" required>
                </div>
              </div>
                
              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="purpose">Purpose</label>
                  <input type="textarea" name="purpose" class="form-control" id="purpose" required>
                </div>
                <div class="col-md-6">
                  <label for="reference">Reference #</label>
                  <input type="text" name="reference_num" class="form-control" id="reference">
                </div>
              </div>

              <div class="row  mb-2">
                <div class="col-md-6">
                  <label for="amount">Amount</label>
                  <input type="number" name="amount" class="form-control" id="amount" required>
                </div>
                <div class="col-md-6">
                  <label for="returned_cash">Returned Cash</label>
                  <input type="number" name="returned_cash" class="form-control" id="returned_cash">
                </div>
              </div>
            <hr><br>
            <?php
              // $query = "SELECT category_name FROM admin_categories";
              // $result = mysqli_query($conn, $query);

              // // Check if there are any categories
              // if (mysqli_num_rows($result) > 0) {
              //     echo '<div class="row mb-2">';
              //     $count = 0;
              //     // Loop through each category
              //     while ($row = mysqli_fetch_assoc($result)) {
              //         $category_name = $row['category_name'];
              //         // Replace characters and convert to lowercase format
              //         $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));

              //         // Output a number input field for the category name
              //         echo '<div class="col-md-4">';
              //         echo '<label for="' . $category_name_replace . '">' . $category_name . '</label>';
              //         echo '<input type="number" name="' . $category_name_replace . '" class="form-control category_amount" id="' . $category_name_replace . '">';
              //         echo '</div>';
              //         $count++;
              //         // If three inputs have been added, close the row and start a new one
              //         if ($count % 3 == 0) {
              //             echo '</div><div class="row mb-2">';
              //         }
              //     }
              //     // Close the last row
              //     echo '</div>';
              // } else {
              //     // If there are no categories
              //     echo 'No categories found.';
              // }
            ?>

              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="categorySelect">Expense:</label>
                  <select name="category_name" class="form-select" id="categorySelect" required>
                    <option value="0" disabled selected>Select Expense</option>
                    <?php
                    $sql = "SELECT * FROM admin_categories";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                      $category_name = $row['category_name'];
                      $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));
                      echo "<option value=\"$category_name_replace\">$category_name</option>";
                    }
                    ?>
                  </select>
                </div>        
                <div class="col-md-6">
                  <label for="category_amount">Amount</label>
                  <input type="number" name="category_amount" class="form-control" id="category_amount" required>
                </div>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="save_record" id="saveRecordBtn"><i class="ti ti-device-floppy fs-3"></i>Save</button>
      </div>
    </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('returned_cash').addEventListener('input', function() {
    var amountInput = document.getElementById('amount');
    var returnedCashInput = document.getElementById('returned_cash');
    var categoryAmountInput = document.getElementById('category_amount');

    // Get the values from the input fields
    var amount = parseFloat(amountInput.value);
    var returnedCash = parseFloat(returnedCashInput.value);

    // If both values are valid, update the category amount
    if (!isNaN(amount) && !isNaN(returnedCash)) {
      var categoryAmount = amount - returnedCash;
      categoryAmountInput.value = categoryAmount;
    }
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("saveRecordBtn").addEventListener("click", function(event) {
        event.preventDefault();
        var employeeSelect = document.getElementById("requestedbySelect");
        if (employeeSelect.value == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select an employee.'
            });
            return;
        }
        
        var categoryAmounts = parseFloat(document.getElementById("category_amount").value) || 0;
        var amount = parseFloat(document.getElementById("amount").value) || 0;
        var returnedCash = parseFloat(document.getElementById("returned_cash").value) || 0;
        var totalAmount = categoryAmounts + returnedCash;
         // Round the total amount to 2 decimal places
         totalAmount = parseFloat(totalAmount.toFixed(2));

        if (amount !== totalAmount) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'The total amount entered in the categories does not match the total amount.'
            });
        } else {
            var form = document.getElementById("addProductForm");
            var buttonNameInput = document.createElement("input");
            buttonNameInput.type = "hidden";
            buttonNameInput.name = "save_record";
            buttonNameInput.value = "save_record";
            form.appendChild(buttonNameInput);
            form.submit();
        }
    });
});
</script>

<?php
  $sql = "SELECT * FROM admin_records";
  if($rs=$conn->query($sql)){
      while ($row=$rs->fetch_assoc()) {
        $id = $row['record_id'];
  ?>
<div class="modal fade modal-xl" id="update-modal<?php echo $row['record_id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Record</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md">
            <form action="functions/update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value ="<?php echo $row['record_id']; ?>" required>

              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="requestedbySelect">Requested By:</label>
                  <select name="requested_by" class="form-select" id="requestedbySelect" required>
                  <?php
                    $id = $row['record_id'];
                    $sql = "SELECT requested_by FROM admin_records WHERE record_id = '$id'";
                    $result = mysqli_query($conn, $sql);
                    while ($row11 = mysqli_fetch_assoc($result)) {
                      $requested_by = $row11['requested_by'];
                  ?>
                    <option value="<?php echo $requested_by; ?>" selected><?php echo $requested_by; ?></option>
                  <?php
                    }
                    $sql = "SELECT * FROM admin_employee";
                    $result = mysqli_query($conn, $sql);
                    while ($row2 = mysqli_fetch_assoc($result)) {
                      $employee_name = $row2['employee_name']; 

                      echo "<option value=\"$employee_name\">$employee_name</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="project_site">Project/Site</label>
                  <input type="textarea" name="project_site" class="form-control" id="project_site" value ="<?php echo $row['project_site']; ?>" required>
                </div>
              </div>
                
              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="purpose">Purpose</label>
                  <input type="textarea" name="purpose" class="form-control" id="purpose" value ="<?php echo $row['purpose']; ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="reference">Reference #</label>
                  <?php 
                  if($row['reference_num'] == "No Receipt"){
                  ?>
                  <input type="text" name="reference_num" class="form-control" id="reference" value="">
                  <?php
                  }else{
                   ?>
                  <input type="text" name="reference_num" class="form-control" id="reference" value="<?php echo $row['reference_num']; ?>">
                  <?php } ?>
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-md-6">
                  <label for="amount">Amount</label>
                  <input type="number" name="amount" class="form-control" id="amount<?php echo $row['record_id']; ?>" value="<?php echo $row['amount']; ?>" required onkeyup="calculateCategoryAmount('<?php echo $row['record_id']; ?>')">
                </div>
                <div class="col-md-6">
                  <label for="returned_cash">Returned Cash</label>
                  <input type="number" name="returned_cash" class="form-control" id="returned_cash<?php echo $row['record_id']; ?>" value="<?php echo $row['returned_cash']; ?>" onkeyup="calculateCategoryAmount('<?php echo $row['record_id']; ?>')">
                </div>
              </div>
            <hr><br>
            <?php
              // $query = "SELECT category_name FROM admin_categories";
              // $result = mysqli_query($conn, $query);

              // if (mysqli_num_rows($result) > 0) {
              //     echo '<div class="row mb-2">';
              //     $count = 0;
              //     $record_columns_query = "SHOW COLUMNS FROM admin_records";
              //     $record_columns_result = mysqli_query($conn, $record_columns_query);

              //     $record_columns = [];
              //     while ($column_row = mysqli_fetch_assoc($record_columns_result)) {
              //         $record_columns[] = $column_row['Field'];
              //     }

              //     while ($row1 = mysqli_fetch_assoc($result)) {
              //         $category_name = $row1['category_name'];
              //         $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));

              //         echo '<div class="col-md-4">';
              //         echo '<label for="' . $category_name_replace . '">' . htmlspecialchars($category_name) . '</label>';

              //         if (in_array($category_name_replace, $record_columns)) {
              //             $value_query = "SELECT $category_name_replace FROM admin_records WHERE record_id = ?";
              //             $stmt = $conn->prepare($value_query);
              //             $stmt->bind_param("i", $id);
              //             $stmt->execute();
              //             $result_value = $stmt->get_result();
              //             $row_value = $result_value->fetch_assoc();
              //             $value = isset($row_value[$category_name_replace]) ? $row_value[$category_name_replace] : '';
              //             echo '<input type="number" name="' . $category_name_replace . '" class="form-control category_amount" value= "'. htmlspecialchars($value) .'" id="' . $category_name_replace . '">';
              //         } else {
              //             echo '<input type="number" name="' . $category_name_replace . '" class="form-control category_amount" value= "" id="' . $category_name_replace . '">';
              //         }

              //         echo '</div>';
              //         $count++;
              //         if ($count % 3 == 0) {
              //             echo '</div><div class="row mb-2">';
              //         }
              //     }
              //     echo '</div>';
              // } else {
              //     echo 'No categories found.';
              // }
              ?>

                      <?php
                        $sql_category1 = "SELECT * FROM admin_categories";
                        $result_category1 = mysqli_query($conn, $sql_category1);
                        while ($category_row1 = mysqli_fetch_assoc($result_category1)) {
                            $category_name = $category_row1['category_name'];
                            $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));         
                            $sql_records = "SELECT $category_name_replace FROM admin_records WHERE record_id = '$id' AND $category_name_replace != 0";
                            $result_records = mysqli_query($conn, $sql_records);
                            if ($row_records = mysqli_fetch_assoc($result_records)) {
                                $category_value = $row_records[$category_name_replace];
                                echo "<input type='text' name='old_category' class='form-control' id='category_amount' value='$category_name_replace' required hidden>";
                            }
                        }
                      ?>    
              <div class="row mb-2">
                <div class="col-md-6">
                    <label for="categorySelect">Expense:</label>
                    <select name="category_name" class="form-select" id="categorySelect" required>
                        <?php
                        $sql_category = "SELECT * FROM admin_categories";
                        $result_category = mysqli_query($conn, $sql_category);
                        while ($category_row = mysqli_fetch_assoc($result_category)) {
                            $category_name = $category_row['category_name'];
                            $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));
                            
                            $sql_records = "SELECT $category_name_replace FROM admin_records WHERE record_id = '$id' AND $category_name_replace != 0";
                            $result_records = mysqli_query($conn, $sql_records);
                                                        if ($row_records = mysqli_fetch_assoc($result_records)) {
                                $category_value = $row_records[$category_name_replace];
                                echo "<option value=\"$category_name_replace\" selected>$category_name</option>";
                            }
                        }
                        ?>
                </div>
                  <?php
                  $sql_category = "SELECT * FROM admin_categories";
                  $result_category = mysqli_query($conn, $sql_category);
                  while ($category_name_row = mysqli_fetch_assoc($result_category)) {
                    $category_name = $category_name_row['category_name'];
                    $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));
                    echo "<option value=\"$category_name_replace\">$category_name</option>";
                  }
                  ?>
                  </select>
                </div>        
                <div class="col-md-6">
                   <?php
                        $sql_category1 = "SELECT * FROM admin_categories";
                        $result_category1 = mysqli_query($conn, $sql_category1);
                        while ($category_row1 = mysqli_fetch_assoc($result_category1)) {
                            $category_name = $category_row1['category_name'];
                            $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));         
                            $sql_records = "SELECT $category_name_replace FROM admin_records WHERE record_id = '$id' AND $category_name_replace != 0";
                            $result_records = mysqli_query($conn, $sql_records);
                            if ($row_records = mysqli_fetch_assoc($result_records)) {
                                $category_value = $row_records[$category_name_replace];
                                echo "<label for='category_amount$id'>Amount</label>";
                                echo "<input type='number' name='category_amount' class='form-control' id='category_amount$id' value='$category_value' required readonly>";
                            }
                        }
                        ?>
                    </div>
              </div>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="update_record"><i class="ti ti-device-floppy fs-3"></i> Update</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php
    }
    }
  ?>

<script>
function calculateCategoryAmount(recordId) {
    var amount = parseFloat(document.getElementById('amount' + recordId).value);
    var returnedCash = parseFloat(document.getElementById('returned_cash' + recordId).value);
    var categoryAmount = amount - returnedCash;
    
    categoryAmount = Math.round(categoryAmount * 100) / 100;
    categoryAmount = categoryAmount.toFixed(2).replace(/\.00$/, "");

    document.getElementById('category_amount' + recordId).value = categoryAmount;
}
</script>


<script>
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('myTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const textValue = cell.textContent || cell.innerText;

                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }

            if (found) {
                rows[i].style.display = '';
                rows[i].classList.remove('highlight');
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
</script>
<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/app.min.js"></script>
<script src="../../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
<script src="../../assets/js/dashboard.js"></script>

</body>
</html>
<?php 
}else{
  header("location: ../../index.php");
  exit;
}
?>