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
  
<title><?php echo $title; ?> | Employee List</title>

  

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
              <h5 class="card-title fw-semibold mb-4">Employee List</h5>
              <div class="flex-grow-1"></div>
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="ti ti-square-plus fs-3"></i>
                Add Employee
              </button>
            </div>
            <div class="table-responsive">
              <table class="table text-nowrap mb-0 align-middle">
                <thead class="text-dark fs-4">
                  <tr>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">ID</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Employee Name</h6>
                    </th>
                    <th class="border-bottom-0">
                      <h6 class="fw-semibold mb-0 text-center">Action</h6>
                    </th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT * FROM admin_employee";
                    if($rs=$conn->query($sql)){
                      $i = 0;
                        while ($row=$rs->fetch_assoc()) {
                          $i++;
                    ?>
                  <tr>
                    <td class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0"><?php echo $i; ?></h6></td>
                    <td class="border-bottom-0 text-center">
                      <p class="mb-0 fw-normal"><?php echo $row['employee_name']; ?></p>
                    </td>
                    <td class="border-bottom-0 d-flex justify-content-center align-items-center">
                        <a href="" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#update-modal<?php echo $row['employee_id']; ?>"><i class="ti ti-edit fs-3"></i> Update</a>
                        <a href="functions/delete_employee.php?id=<?php echo $row['employee_id']; ?>" class="btn btn-sm btn-danger"><i class="ti ti-trash fs-3"></i> Delete</a>
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

<!-- Add employee -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add a Employee</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md">
            <form action="functions/add.php" method="post">
            <div class="form-floating">
              <input type="text" name="employee_name" class="form-control" id="floatingInputGrid" required>
              <label for="floatingInputGrid">Employee Name</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="save_employee"><i class="ti ti-device-floppy fs-3"></i> Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php
  $sql = "SELECT * FROM admin_employee";
  if($rs=$conn->query($sql)){
      while ($row=$rs->fetch_assoc()) {

  ?>
<!-- Update employee -->
<div class="modal fade" id="update-modal<?php echo $row['employee_id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Employee</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md">
            <form action="functions/update.php" method="post">
            <div class="form-floating">
              <input type="hidden" name="employee_id" value="<?php echo $row['employee_id'] ?>">
              <input type="text" name="employee_name" class="form-control" id="floatingInputGrid" value="<?php echo $row['employee_name'] ?>">
              <label for="floatingInputGrid">Employee Name</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="update_employee"><i class="ti ti-device-floppy fs-3"></i> Update</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php
    }
    }
  ?>
<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/sidebarmenu.js"></script>
<script src="../../assets/js/app.min.js"></script>


</body>
</html>
<?php 
}else{
  header("location: ../../index.php");
  exit;
}
?>