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
  
  <title><?php echo $title; ?> | Dashboard</title>

  

  <!-- Main Template -->
  <link rel="stylesheet" href="../../assets/css/styles.min.css">
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
<?php include '../admin/components/navigation.php'; ?>

  <!--  Main wrapper -->
  <div class="body-wrapper">

  <?php include '../admin/components/header.php'; ?>

  <?php
    $stmt = $conn->prepare("SELECT COUNT(product_name) FROM ims_products WHERE status = ? AND product_id IS NOT NULL");

    // Bind parameter for the status
    $stmt->bind_param("s", $status);

    $status = 'Usable';
    $stmt->execute();
    $stmt->bind_result($usable_products);
    $stmt->fetch();

    $status = 'Defective';
    $stmt->execute();
    $stmt->bind_result($defective_products);
    $stmt->fetch();

    $status = 'Inactive';
    $stmt->execute();
    $stmt->bind_result($inactive_products);
    $stmt->fetch();

    $stmt->close();
?>

    <div class="container-fluid">
        <div class="row row-cols-lg-3 row-cols-md-3 g-2">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <h3 class="card-title text-light">Usable Products</h3>
                    <h5 class="card-text text-light my-1">
                        <span id="sample-count"><?php echo isset($usable_products) ? number_format($usable_products) : 0; ?></span>
                    </h5>
                    <a href="stocks.php" class="btn btn-sm btn-light mt-2 text-dark text-decoration-none">Click here to see more information</a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <h3 class="card-title text-light">Defective Products</h3>
                    <h5 class="card-text text-light my-1">
                        <span id="sample-count"><?php echo isset($defective_products) ? number_format($defective_products) : 0; ?></span>
                    </h5>
                    <a href="stocks.php" class="btn btn-sm btn-light mt-2 text-dark text-decoration-none">Click here to see more information</a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <h3 class="card-title text-light">Inactive Products</h3>
                    <h5 class="card-text text-light my-1">
                        <span id="sample-count"><?php echo isset($inactive_products) ? number_format($inactive_products) : 0; ?></span>
                    </h5>
                    <a href="stocks.php" class="btn btn-sm btn-light mt-2 text-dark text-decoration-none">Click here to see more information</a>
                </div>
            </div>
        </div>
    </div>

        
    <div class="card w-100">
      <div class="card-body p-4">
        <div class="d-flex">
          <div class="flex-grow-1 ">
            <h5 class="card-title fw-semibold mb-4 text-center">Logs</h5>
          </div>
        </div>
        <div class="table-responsive">
          <table id="myTable" class="table text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
              <tr>
                <th class="border-bottom-0">
                  <h6 class="fw-semibold mb-0"></h6>
                </th>
                <th class="border-bottom-0">
                  <h6 class="fw-semibold mb-0 text-center">Subject</h6>
                </th>
                <th class="border-bottom-0">
                  <h6 class="fw-semibold mb-0 text-center">Description</h6>
                </th>
                <th class="border-bottom-0">
                  <h6 class="fw-semibold mb-0 text-center">Date Modified</h6>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php
              $recordsPerPage = 10; // Set the number of records per page
              $page = isset($_GET['page']) ? $_GET['page'] : 1;
              $offset = ($page - 1) * $recordsPerPage;
    
              $sql = "SELECT * FROM ims_logs ORDER BY date_created DESC LIMIT $offset, $recordsPerPage";
              if ($rs = $conn->query($sql)) {
                  $i = $offset;
                  while ($row = $rs->fetch_assoc()) {
                      $i++;
              ?>
                <tr>
                  <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-0"><?php echo $i; ?></h6>
                  </td>
                  <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-0"><?php echo $row['subject']; ?></h6>
                  </td>
                  <td class="border-bottom-0 text-wrap text-center">
                    <h6 class="fw-semibold mb-0"><?php echo $row['description']; ?></h6>
                  </td>
                  <td class="border-bottom-0 text-center">
                    <h6 class="fw-semibold mb-0"><?php
                      $date = new DateTime($row['date_created']);
                      $formattedDate = $date->format('F j, Y' . ' @ ' . 'h:i A');
                      echo $formattedDate;
                      ?>
                    </h6>
                  </td>
                </tr>
              <?php
                  }
              }
              ?>
            </tbody>
          </table>
    
          <?php
          // Pagination buttons
          $sql = "SELECT COUNT(*) AS total FROM ims_logs";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();
          $totalPages = ceil($row["total"] / $recordsPerPage);
    
          if ($totalPages > 1) {
          ?>
    
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <?php
                for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($page == $i) ? 'active' : '';
                ?>
                  <li class="page-item <?php echo $activeClass; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                  </li>
                <?php
                }
                ?>
              </ul>
            </nav>
    
          <?php
          }
          ?>
    
        </div>
      </div>
    </div>





  </div>
</div>
  </div>
  <script>
    // Find all elements with class 'custom-card'
    var sampleCountBoxes = document.querySelectorAll(".custom-card");

    // Loop through each box
    sampleCountBoxes.forEach(function(box) {
      var h3 = box.querySelector("h3");

      // Check the text content of the h1 element and change the background color accordingly
      if (h3.textContent === 'Usable Products') {
        box.style.backgroundColor = '#77DD77';
      } else if (h3.textContent === 'Defective Products') {
        box.style.backgroundColor = '#FFB347';
      } else if (h3.textContent === 'Inactive Products') {
        box.style.backgroundColor = '#FF6961';
      }
    });
  </script>
  
  <script>
      $(function () {
    "use strict";
    var url = window.location.href;
    var baseUrl = url.split('?')[0]; // Extract the base URL without query parameters

    var element = $("ul#sidebarnav a").filter(function () {
        return this.href === url || this.href === baseUrl;
    });

    element.addClass("active");
    });

  </script>
  
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