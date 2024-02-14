<!--  Header Start -->
<?php
$privilege = $_SESSION["privilege"];
$sql = "SELECT * FROM admin_login WHERE privilege = '$privilege'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $username = $row['username'];
  $fullname = $row['full_name'];
  $profile = $row['profile'];
  $password = $row['password'];
      if (!empty($profile)) {
        // Convert the BLOB data to base64 encoding
        $src = 'functions/' . $profile;
    } else {
        // If the image is not available, show a default image
        $src = "functions/uploads/default.png";
    }
  } else {
  // If no matching record is found, show a default image
  $src = "functions/uploads/default.png";
  }

  
  ?>
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

        <div class="btn-group">
          <a class="nav-link nav-icon-hover cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo $src; ?>" alt="" width="35" height="35" class="rounded-circle">
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up">
            <li>
              <button class="d-flex align-items-center gap-2 dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#update-modal">
                <i class="ti ti-mail fs-6"></i>
                <p class="mb-0 fs-3">My Account</p>
              </button>
            </li>
            <li>
              <button class="d-flex align-items-center gap-2 dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#update-system">
                <i class="ti ti-settings fs-6"></i>
                <p class="mb-0 fs-3">System Modification</p>
              </button>
            </li>
            <li>
              <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block"><i class="ti ti-logout"></i> Logout</a>
            </li>
          </ul>
        </div>
      </ul>
    </div>
  </nav>
</header>
<!--  Header End -->

<?php
  $sql5 = "SELECT * FROM ims_designs";
  $result5 = $conn->query($sql5);

  if ($result5->num_rows > 0) {
    $row5 = $result5->fetch_assoc();
    $logo = $row5['logo'];
    $title = $row5['title'];

        if (!empty($logo)) {
          // Convert the BLOB data to base64 encoding
          $logo_default = 'functions/' . $logo;
      } else {
          // If the image is not available, show a default image
          $logo_default = "functions/uploads/default.png";
      }
    } else {
    // If no matching record is found, show a default image
    $logo_default = "functions/uploads/default.png";
    }
?>
<div class="modal fade" id="update-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="update-modal-label">Update My Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="functions/update.php" method="post" enctype="multipart/form-data">
      <div class="modal-body">
        <!-- Existing code for displaying the profile picture -->
        <div class="shadow border border-opacity-50 mt-2" style="width: 200px; height: 200px; margin: 0 auto; text-align: center; display: flex; align-items: center; justify-content: center;">
            <img src="<?php echo $src; ?>" style="max-width: 100%; max-height: 100%;" class="profile-image">
        </div>

        <input type="text" class="form-control" name="old_profile_image" value="<?php echo $src; ?>" hidden readonly>
        <!-- File input to upload the new profile picture -->
        <input type="file" name="profile_image" accept="image/*" class="form-control form-control-sm mt-4 profile-image-input mb-3">
        <script>
            document.querySelectorAll('.profile-image-input').forEach(function (fileInput, index) {
                fileInput.addEventListener('change', function () {
                    var idboxImage = document.querySelectorAll('.profile-image')[index];

                    if (fileInput.files && fileInput.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            idboxImage.src = e.target.result;
                        };

                        reader.readAsDataURL(fileInput.files[0]);
                    } else {
                        // If no file is selected or selection is canceled, revert to the original profile picture
                        idboxImage.src = '<?php echo $src; ?>';
                    }
                });
            });
        </script>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="old_fullname" value="<?php echo $fullname; ?>" hidden readonly>
          <input type="text" class="form-control" name="fullname" value="<?php echo $fullname; ?>">
          <label for="floatingInput">Full Name</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="old_username" value="<?php echo $username; ?>" hidden readonly>
          <input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
          <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating">
          <input type="text" class="form-control" name="old_password" value="<?php echo $password; ?>" hidden readonly>
          <input type="password" class="form-control" id="" name="password" minlength="6">
          <label for="floatingPassword">Password</label>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="update_account"><i class="ti ti-edit fs-3"></i> Update</button>
      </div>
    </div>
    </form>
  </div>
</div>

<div class="modal fade" id="update-system" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="update-modal-label">Update System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="functions/update.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
                <div class="text-center">
                <label class="form-label">Change Logo</label>
                <div class="shadow border border-opacity-50 mt-2" style="width: 200px; height: 200px; margin: 0 auto; text-align: center; display: flex; align-items: center; justify-content: center;">
            <img src="<?php echo $logo_default; ?>" style="max-width: 100%; max-height: 100%;" class="profile-image1">
        </div>
          <!-- File input to upload the new profile picture -->
          <input type="text" class="form-control" name="old_logo_default" value="<?php echo $logo_default; ?>" hidden readonly>
          <input type="file" name="profile_image1" accept="image/*" class="form-control form-control-sm mt-4 profile-image-input1">
          <script>
              document.querySelectorAll('.profile-image-input1').forEach(function (fileInput1, index) {
                  fileInput1.addEventListener('change', function () {
                      var idboxImage1 = document.querySelectorAll('.profile-image1')[index];

                      if (fileInput1.files && fileInput1.files[0]) {
                          var reader1 = new FileReader();

                          reader1.onload = function (e) {
                              idboxImage1.src = e.target.result;
                          };

                          reader1.readAsDataURL(fileInput1.files[0]);
                      } else {
                          // If no file is selected or selection is canceled, revert to the original profile picture
                          idboxImage1.src = '<?php echo $logo_default; ?>';
                      }
                  });
              });
          </script>
          <label class="form-label mt-4">Change System Name</label>
          <input type="text" class="form-control" name="old_title" value="<?php echo $title; ?>" hidden readonly>
          <input class="form-control" type="text" name="title" value="<?php echo $title; ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x fs-3"></i> Close</button>
        <button type="submit" class="btn btn-primary" name="update_design"><i class="ti ti-edit fs-3"></i> Update</button>
      </div>
    </div>
    </form>
  </div>
</div>
