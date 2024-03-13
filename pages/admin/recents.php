<?php

session_start();

include '../../conn.php';

if(isset($_SESSION["loggedinasadmin"]) || isset($_SESSION["loggedinasmainuser"])){
  
  if(isset($_SESSION['response'])) {
    $response = $_SESSION['response'];
    unset($_SESSION['response']); // Clear the session after retrieving the response
  }else{
    $response="";
  }
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
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
                        <a class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#update-modal-<?php echo $row['record_id']; ?>"><i class="ti ti-edit fs-3"></i> Update</a>
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

<!-- DO NOT TOUCH ANYTHING IN THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING IN THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING IN THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING IN THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING IN THIS CODE!!!!!!! -->

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
                  <label for="add_requested_by">Requested By:</label>
                  <select name="add_requested_by" class="form-select" id="add_requested_by" required>
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
                  <label for="add_project_site">Project/Site</label>
                  <input type="textarea" name="add_project_site" class="form-control" id="add_project_site" required>
                </div>
              </div>
                
              <div class="row mb-2">
                <div class="col-md-12">
                  <label for="add_purpose">Purpose</label>
                  <input type="textarea" name="add_purpose" class="form-control" id="add_purpose" required>
                </div>
              </div>

              <div class="row  mb-2">
                <div class="col-md-6">
                  <label for="add_amount">Amount</label>
                  <input type="number" name="add_amount" class="form-control" id="add_amount" required>
                </div>
                <div class="col-md-6">
                  <label for="add_returned_cash">Returned Cash</label>
                  <input type="number" name="add_returned_cash" class="form-control" id="add_returned_cash">
                </div>
              </div>
            <hr><br>
              <div class="row mb-2">
                <div class="col-md-2">
                  <label for="add_supplier_name">Supplier Name</label>
                  <input type="text" name="add_supplier_name[]" class="form-control" id="add_supplier_name" required>
                </div>
              <div class="col-md-5">
                  <label for="add_address">Address</label>
                  <input type="textarea" name="add_address[]" class="form-control" id="add_address" required>
                </div>
                <div class="col-md-3">
                  <label for="categorySelect">Expense:</label>
                  <select name="add_category_name[]" class="form-select" id="categorySelect" required>
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
                <div class="col-md-2">
                  <label for="add_category_amount">Amount</label>
                  <input type="number" name="add_category_amount[]" class="form-control" id="add_category_amount" required>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-md-2">
                  <label for="add_tin">TIN</label>
                  <input type="text" name="add_tin[]" class="form-control" id="add_tin" required>
                </div>
              <div class="col-md-2">
                  <label for="add_doctype">Document Type</label>
                  <select name="add_doc_type[]" class="form-select" id="add_doctype" required>
                    <option value="0" disabled selected>Select Document Type</option>
                    <option value="OR">OR</option>
                    <option value="SI">SI</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label for="add_doc_num">Doc Number</label>
                  <input type="text" name="add_doc_num[]" class="form-control" id="add_doc_num">
                </div>      
                <div class="col-md-2">
                  <label for="add_goods_service_others">Goods/Service/Others</label>
                  <select name="add_goods_service_others[]" class="form-select" id="add_goods_service_others" required>
                    <option value="0" disabled selected>Please Select</option>
                    <option value="Goods">Goods</option>
                    <option value="Service">Service</option>
                    <option value="Others">Others</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="add_particulars">Particulars</label>
                  <input type="text" name="add_particulars[]" class="form-control" id="add_particulars">
                </div>
              </div>
              <hr><br>

              <div class="row mb-2">
                <div class="col-md-4">
                  <button type="button" class="btn btn-secondary" id="btn_add_expenses">ADD EXPENSES</button>
                </div>      
                <div class="col-md-4">
                  <button type="button" class="btn btn-secondary" id="btn_remove_expenses">REMOVE PREVIOUS</button>
                </div>        
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="save_record" id="saveRecordBtn"><i class="ti ti-device-floppy fs-3"></i>Save</button>
      </div>

      <script>
    $(document).ready(function() {
        // Function to populate expense select options
        function populateExpenseOptions() {
            var options = '<option value="0" disabled selected>Select Expense</option>';
            <?php
            $sql = "SELECT * FROM admin_categories";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $category_name = $row['category_name'];
                $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));
                echo "options += '<option value=\"$category_name_replace\">$category_name</option>';";
            }
            ?>
            $('#categorySelect').html(options);
        }

        // Initial population of expense select options
        populateExpenseOptions();

        // Click event handler for adding expenses
        $('#btn_add_expenses').click(function() {
            var newExpenseFields = `
            <div class="added-expense">
            <br>
            <div class="row mb-2">
                <div class="col-md-2">
                  <label for="add_supplier_name">Supplier Name</label>
                  <input type="text" name="add_supplier_name[]" class="form-control" id="add_supplier_name" required>
                </div>
              <div class="col-md-5">
                  <label for="add_address">Address</label>
                  <input type="textarea" name="add_address[]" class="form-control" id="add_address" required>
                </div>
                <div class="col-md-3">
                  <label for="categorySelect">Expense:</label>
                  <select name="add_category_name[]" class="form-select" id="categorySelect" required>
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
                <div class="col-md-2">
                  <label for="add_category_amount">Amount</label>
                  <input type="number" name="add_category_amount[]" class="form-control" id="add_category_amount" required>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-md-2">
                  <label for="add_tin">TIN</label>
                  <input type="text" name="add_tin[]" class="form-control" id="add_tin" required>
                </div>
              <div class="col-md-2">
                  <label for="add_doctype">Document Type</label>
                  <select name="add_doc_type[]" class="form-select" id="add_doctype" required>
                    <option value="0" disabled selected>Select Document Type</option>
                    <option value="OR">OR</option>
                    <option value="SI">SI</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label for="add_doc_num">Doc Number</label>
                  <input type="text" name="add_doc_num[]" class="form-control" id="add_doc_num">
                </div>      
                <div class="col-md-2">
                  <label for="add_goods_service_others">Goods/Service/Others</label>
                  <select name="add_goods_service_others[]" class="form-select" id="add_goods_service_others" required>
                    <option value="0" disabled selected>Please Select</option>
                    <option value="Goods">Goods</option>
                    <option value="Service">Service</option>
                    <option value="Others">Others</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="add_particulars">Particulars</label>
                  <input type="text" name="add_particulars[]" class="form-control" id="add_particulars">
                </div>
              </div>
              <hr>
              </div>
            `;

            $('#btn_add_expenses').parent().before(newExpenseFields);

            // Repopulate expense select options
            populateExpenseOptions();
        });

        // Click event handler for removing expenses
        $('#btn_remove_expenses').click(function() {
            // Select the container of the last added expense and remove it
            var lastExpenseContainer = $('.added-expense').last();
            if (lastExpenseContainer.length > 0) {
                lastExpenseContainer.remove();
            }
        });
    });
</script>

    </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('add_returned_cash').addEventListener('input', function() {
    var amountInput = document.getElementById('add_amount');
    var returnedCashInput = document.getElementById('add_returned_cash');
    var categoryAmountInput = document.getElementById('add_category_amount');

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
          var employeeSelect = document.getElementById("add_requested_by");
          if (employeeSelect.value == 0) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Please select an employee.'
              });
              return;
          }

          var categoryAmountInputs = document.querySelectorAll('[name="add_category_amount[]"]');
          var totalCategoryAmount = 0;
          var categoryAmountsText = "";

          categoryAmountInputs.forEach(function(input) {
              var categoryAmount = parseFloat(input.value) || 0;
              totalCategoryAmount += categoryAmount;
              categoryAmountsText += "Category Amount: " + categoryAmount.toFixed(2) + "\n";
          });

          var amount = parseFloat(document.getElementById("add_amount").value) || 0;
          var returnedCash = parseFloat(document.getElementById("add_returned_cash").value) || 0;
          var totalAmount = amount + returnedCash;
          // Round the total amount to 2 decimal places
          totalAmount = parseFloat(totalAmount.toFixed(2));

          if (totalCategoryAmount !== totalAmount) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'The total amount entered in the categories does not match the total amount.\n\n' + categoryAmountsText
              });
          } else {
              var form = document.getElementById("addProductForm");
              var buttonNameInput = document.createElement("input");
              buttonNameInput.type = "hidden";
              buttonNameInput.name = "save_record";
              buttonNameInput.value = "save_record";
              form.appendChild(buttonNameInput);
              
              // Submit the form using AJAX to handle the response
              $.ajax({
                  url: form.action,
                  method: form.method,
                  data: $(form).serialize(),
                  success: function(response) {
                      // Show success SweetAlert
                      Swal.fire({
                          icon: 'success',
                          title: 'Success',
                          text: 'Record added successfully.',
                          onClose: function() {
                              // Redirect or perform any other action after success
                              window.location.href = 'sample.php';
                          }
                      });
                  },
                  error: function(xhr, status, error) {
                      // Show error SweetAlert
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Error adding record: ' + error
                      });
                  }
              });
          }
      });
  });
</script>
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->
<!-- DO NOT TOUCH ANYTHING ABOVE THIS CODE!!!!!!! -->


  <?php
  $sql = "SELECT * FROM admin_records";
  if ($rs = $conn->query($sql)) {
      while ($row = $rs->fetch_assoc()) {
          $id = $row['record_id'];
  ?>
  <div class="modal fade modal-xl" id="update-modal-<?php echo $row['record_id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Record</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row g-2">
                      <div class="col-md">
                          <form action="./functions/update.php" method="post" id="updateRecordForm-<?php echo $row['record_id']; ?>" enctype="multipart/form-data">
                              <input type="hidden" name="id" value="<?php echo $row['record_id']; ?>" required>

                              <div class="row mb-2">
                                  <div class="col-md-6">
                                      <label>Requested By:</label>
                                      <select name="requested_by" class="form-select" required>
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
                                      <label>Project/Site</label>
                                      <input type="textarea" name="project_site" class="form-control" value="<?php echo $row['project_site']; ?>" required>
                                  </div>
                              </div>

                              <div class="row mb-2">
                                  <div class="col-md-12">
                                      <label>Purpose</label>
                                      <input type="textarea" name="purpose" class="form-control" value="<?php echo $row['purpose']; ?>" required>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-md-6">
                                      <label for="amount">Amount</label>
                                      <input type="number" name="amount" class="form-control" value="<?php echo $row['amount']; ?>" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="returned_cash">Returned Cash</label>
                                      <input type="number" name="returned_cash" class="form-control" value="<?php echo $row['returned_cash']; ?>">
                                  </div>
                              </div>
                              <hr><br>
                              <div id="expense-details-<?php echo $row['record_id']; ?>">
                                <?php 
                                    $record_id = $row['record_id'];
                                    $sql_details = "SELECT * FROM admin_record_details WHERE record_id = '$record_id'";
                                    $res_details = $conn->query($sql_details);

                                    while($row_details = mysqli_fetch_assoc($res_details)){
                                        $supplier = $row_details['supplier_name'];
                                        $address = $row_details['address'];
                                        $tin = $row_details['tin'];
                                        $doc_type = $row_details['doc_type'];
                                        $doc_num = $row_details['doc_num'];
                                        $goods_service_others = $row_details['goods_service_others'];
                                        $particulars = $row_details['particulars'];
                                        $category_name = $row_details['category_names'];
                                        $category_amount = $row_details['category_amounts'];

                                        // Separate data into arrays
                                        $supplier_array = explode(", ", $supplier);
                                        $address_array = explode(", ", $address);
                                        $tin_array = explode(", ", $tin);
                                        $doc_type_array = explode(", ", $doc_type);
                                        $doc_num_array = explode(", ", $doc_num);
                                        $goods_service_others_array = explode(", ", $goods_service_others);
                                        $particulars_array = explode(", ", $particulars);
                                        $category_name_array = explode(", ", $category_name);
                                        $category_amount_array = explode(", ", $category_amount);
                                        
                                        
                                        
                                        foreach($supplier_array as $key => $value){
                                          $category_name_str =  $category_name_array[$key];
                                            echo '<div class="row mb-2">';
                                            echo '<div class="col-md-2">';
                                            echo '<label>Supplier Name</label>';
                                            echo '<input type="text" name="update_supplier_name['.$key.']" class="form-control" value="' . $value . '" required>';
                                            echo '</div>';
                                            echo '<div class="col-md-5">';
                                            echo '<label for="returned_cash">Address</label>';
                                            echo '<input type="textarea" name="update_address['.$key.']" class="form-control" value="'. $address_array[$key] .'">';
                                            echo '</div>';

                                            // Start of first column
                                            echo '<div class="col-md-3">';
                                            echo '<label>Expense:</label>';
                                            echo '<input type="text" name="old_category_name['.$key.']" class="form-control" value="'. $category_name_str .'" hidden>';
                                            echo '<select name="update_category_name['.$key.']" class="form-select" required>';
                                          
                                            $sql_category_str = "SELECT category_name FROM admin_categories WHERE category_change = '$category_name_str'";
                                            $result_category_str = mysqli_query($conn, $sql_category_str);
                                            
                                            $row_str = mysqli_fetch_assoc($result_category_str);
                                            $category_named = $row_str['category_name'];
                                            echo '<option value="'. $category_name_array[$key] .'" selected>'. $category_named .'</option>';

                                            $sql_category1 = "SELECT * FROM admin_categories";
                                            $result_category1 = mysqli_query($conn, $sql_category1);

                                            while ($category_row1 = mysqli_fetch_assoc($result_category1)) {
                                                $category_name1 = $category_row1['category_name'];
                                                $category_name_replace1 = strtolower(str_replace([' - ', ', ', ' / ', '-', ',', '/', ' '], '_', $category_name1));

                                                // Add selected option
                                                echo "<option value=\"$category_name_replace1\">$category_name1</option>";
                                            }
                                            echo '</select>';
                                            echo '</div>'; 
                                            echo '<div class="col-md-2">';
                                            echo '<label>Amount</label>';
                                            echo '<input type="number" name="update_category_amount['.$key.']" class="form-control" value="' . $category_amount_array[$key] . '" required>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '<div class="row mb-2">';
                                            echo '<div class="col-md-2">';
                                            echo '<label>TIN</label>';
                                            echo '<input type="text" name="update_tin['.$key.']" value="'. $tin_array[$key] .'" class="form-control" required>';
                                            echo '</div>';
                                            echo '<div class="col-md-2">';
                                            echo '<label>Document Type</label>';
                                            echo '<select name="update_doc_type['.$key.']" class="form-select" required>';
                                            echo '<option value="'. $doc_type_array[$key] .'" selected>'. $doc_type_array[$key] .'</option>';
                                            echo '<option value="OR">OR</option>';
                                            echo ' <option value="SI">SI</option>';
                                            echo '</select>';
                                            echo '</div>';
                                            echo '<div class="col-md-2">';
                                            echo '<label>Doc Number</label>';
                                            echo '<input type="text" name="update_doc_num['.$key.']" value="'. $doc_num_array[$key] .'" class="form-control">';
                                            echo '</div>';     
                                            echo '<div class="col-md-2">';
                                            echo '<label>Goods/Service/Others</label>';
                                            echo '<select name="update_goods_service_others['.$key.']" class="form-select" required>';
                                            echo '<option value="'. $goods_service_others_array[$key] .'" selected>'. $goods_service_others_array[$key] .'</option>';
                                            echo '<option value="Goods">Goods</option>';
                                            echo '<option value="Service">Service</option>';
                                            echo '<option value="Others">Others</option>';
                                            echo '</select>';
                                            echo '</div>';
                                            echo '<div class="col-md-4">';
                                            echo '<label>Particulars</label>';
                                            echo '<input type="text" name="update_particular['.$key.']" value="'. $particulars_array[$key] .'" class="form-control">';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '<hr><br>';
                                        }
                                    }
                                ?>
                            </div>
                              <div class="row mb-2">
                                  <div class="col-md-4">
                                      <button type="button" class="btn btn-secondary btn_add_expenses" data-record-id="<?php echo $row['record_id']; ?>">ADD EXPENSES</button>
                                  </div>
                                  <div class="col-md-4">
                                      <button type="button" class="btn btn-secondary btn_remove_expenses" data-record-id="<?php echo $row['record_id']; ?>">REMOVE PREVIOUS</button>
                                  </div>
                              </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
                  <button type="submit" class="btn btn-primary" name="update_record" id="updateRecordBtn-<?php echo $row['record_id']; ?>"><i class="ti ti-device-floppy fs-3"></i> Update</button>
              </div>
              </form>
          </div>
      </div>
  </div>
  </div>

  <?php
      }
  }
  ?>
  <?php
  // Check if the 'response' variable is set and it's an array
  if(isset($response) && is_array($response)) {
      // Check if the 'success' key exists in the response array and it's true
      if(isset($response["success"]) && $response["success"]) {
          // Display success message using SweetAlert
          echo "<script>Swal.fire('Success', '{$response["message"]}', 'success');</script>";
      } 
      // Check if the 'failed' key exists in the response array and it's true
      elseif(isset($response["failed"]) && $response["failed"]) {
          // Display error message using SweetAlert
          echo "<script>Swal.fire('Error', '{$response["message"]}', 'error');</script>";
      }
  }
  ?>
<script>
$(document).ready(function() {
    // Click event handler for adding expenses
    $('.btn_add_expenses').click(function() {
        var recordId = $(this).data('record-id');
        var newExpenseFields = `
        <div class="added-expense">
        <br>
        <div class="row mb-2">
            <div class="col-md-2">
                <label>Supplier Name</label>
                <input type="text" name="update_supplier_name[]" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label>Address</label>
                <input type="textarea" name="update_address[]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Expense:</label>
                <select name="update_category_name[]" class="form-select" required>
                    <option value="0" disabled selected>Select Expense</option>
                    <?php
                    $sql = "SELECT * FROM admin_categories";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $category_name = $row['category_name'];
                        $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',', '/', ' '], '_', $category_name));
                        echo "<option value=\"$category_name_replace\">$category_name</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Amount</label>
                <input type="number" name="update_category_amount[]" class="form-control" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-2">
                <label>TIN</label>
                <input type="text" name="update_tin[]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>Document Type</label>
                <select name="update_doc_type[]" class="form-select" required>
                    <option value="0" disabled selected>Select Document Type</option>
                    <option value="OR">OR</option>
                    <option value="SI">SI</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Doc Number</label>
                <input type="text" name="update_doc_num[]" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Goods/Service/Others</label>
                <select name="update_goods_service_others[]" class="form-select" required>
                    <option value="0" disabled selected>Please Select</option>
                    <option value="Goods">Goods</option>
                    <option value="Service">Service</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Particulars</label>
                <input type="text" name="update_particular[]" class="form-control">
            </div>
        </div>
        <hr>
        </div>
        `;

        $('#expense-details-' + recordId).append(newExpenseFields);
    });

    // Click event handler for removing expenses
    $('.btn_remove_expenses').click(function() {
        var recordId = $(this).data('record-id');
        $('#expense-details-' + recordId + ' .added-expense:last').remove();
    });
});
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

  <script>
  //     setTimeout(function() {
  //   // Clear the console after 1 second
  //   console.clear();
  // }, 100);

  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');

  if (id) {
      // Output debug information
      console.log(`Showing modal for ID: ${id}`);
      
      // Use JavaScript to trigger the modal based on the 'id'
      $(document).ready(function() {
          $(`#update-modal-${id}`).modal('show');
      });
  }

  window.onload = function() {
      history.replaceState({}, document.title, window.location.pathname);
  }
  </script> 

  </body>
  </html>
  <?php 
  }else{
    header("location: ../../index.php");
    exit;
  }
  ?>