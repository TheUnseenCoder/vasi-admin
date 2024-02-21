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
  
  <title><?php echo $title; ?> | Monthly Records</title>

  
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
            <h5 class="card-title fw-semibold mb-4"> Monthly Records</h5>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="ti ti-file-export fs-6"></i>
                 Export Excel
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
                      <h6 class="fw-semibold mb-0 text-center">Action</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $current_month = date('m');
                    $current_year = date('Y');
                    $sql = "SELECT * FROM admin_records WHERE MONTH(date_encoded) = '$current_month' AND YEAR(date_encoded) = '$current_year' ORDER BY date_encoded DESC";
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

<!-- DATE MODAL -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Export Excel</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md">
            <form action="functions/download_excel.php" method="post">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="from_date">From:</label>
                        <input type="date" name="from_date" class="form-control" id="from_date" required>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date">To:</label>
                        <input type="date" name="to_date" class="form-control" id ="to_date" required>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="save_user"><i class="ti ti-device-floppy fs-3"></i> Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Main Template -->
<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Your other scripts -->
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/app.min.js"></script>
<script src="../../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
<script src="../../assets/js/dashboard.js"></script>

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


</body>
</html>
<?php 
}else{
  header("location: ../../index.php");
  exit;
}
?>