<?php
include '../conn.php';

// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION["loggedinasadmin"]) && $_SESSION["loggedinasadmin"] == true) {
 // User is logged in, redirect to the dashboard
 header("Location: admin/dashboard.php");
 exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
  $sql5 = "SELECT * FROM ims_designs";
  $result5 = $conn->query($sql5);

  if ($result5->num_rows > 0) {
    $row5 = $result5->fetch_assoc();
    $logo = $row5['logo'];
    $tabhead = $row5['tabhead'];
    $title = $row5['title'];

        if (!empty($logo)) {
          // Convert the BLOB data to base64 encoding
          $logo_default = 'admin/functions/' . $logo;
          $tabhead = 'admin/functions/' . $tabhead;
      } else {
          // If the image is not available, show a default image
          $logo_default = "admin/functions/uploads/default.png";
      }
    } else {
    // If no matching record is found, show a default image
    $logo_default = "admin/functions/uploads/default.png";
    }
?>
<head>
    <link rel="icon" href="<?php echo $tabhead; ?>">
</head>

 <title><?php echo $title; ?> - Sign - In</title>
 
 <!-- Font Awesome Icons -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
 
 <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<style>
.toggle-password {
  position: absolute;
  top: 65%;
  transform: translateY(-50%);
  right: 10px;
  cursor: pointer;
}
</style>
<body>
 <!-- Body Wrapper -->
 <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
  data-sidebar-position="fixed" data-header-position="fixed">
  <div
   class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
   <div class="d-flex align-items-center justify-content-center w-100">
    <div class="row justify-content-center w-100">
     <div class="col-md-8 col-lg-6 col-xxl-3">
      <div class="card mb-0">
          
       <div class="card-body">
        <div class="text-center">
         <img src="<?php echo $logo_default; ?>" alt="logo" class="img-fluid w-100"/>
          <div class="d-block py-3 w-100">
           <label class="fs-5 fw-semibold text-primary">VASI ADMIN</label>
          </div>
        </div>
        <?php
        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check username and password (replace these with your actual authentication logic)
        $input_username = $_POST["username"];
        $input_password = md5($_POST["password"]); // Hash the entered password
        $status = "enabled";
        // Perform a query to find the user with the given username
        $sql = "SELECT username, password, full_name, privilege FROM admin_login WHERE username = '$input_username' AND status = '$status'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
         $row = $result->fetch_assoc();
         $username = $row['username'];
         $password = $row['password'];
         $fullname = $row['full_name'];
         $privilege = $row['privilege'];

         // Verify the entered password against the hashed password in the database
         if ($input_password == $password) {
          // Successful login, redirect to a secure page

          if($privilege == "Administrator"){
            $_SESSION["privilege"] = $privilege;
            $_SESSION["fullname"] = $fullname;
            $_SESSION["loggedinasadmin"] = true;
            header("Location: admin/dashboard.php");
            exit();
          }
          elseif($privilege == "Main User"){
            $_SESSION["privilege"] = $privilege;
            $_SESSION["fullname"] = $fullname;
            $_SESSION["loggedinasmainuser"] = true;
            header("Location: admin/dashboard.php");
            exit();
          }
         }
        }

        // Display an error message
        echo "<p style='color: red;'>Invalid username or password.</p>";
        }
         ?>
         <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
           <div class="mb-3">
             <label for="exampleInputEmail1" class="form-label">Username</label>
             <input type="text" name="username" class="form-control" id="exampleInputEmail1" required>
           </div>
           <div class="mb-4 position-relative">
             <label for="exampleInputPassword1" class="form-label">Password</label>
             <input type="password" name="password" class="form-control" id="exampleInputPassword1" required>
             <span class="toggle-password mt-1" id="togglePassword"><i class="fa-regular fa-eye"></i></span>
            </div>
           <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2" name="login">Sign In</button>
         </form>
         <div class="text-center">
          <a href="user/">Sign in as User</a>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
 <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
 <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
 <script>
    const passwordInput = document.getElementById('exampleInputPassword1');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePassword.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
        } else {
            passwordInput.type = 'password';
            togglePassword.innerHTML = '<i class="fa-regular fa-eye"></i>';
        }
    });
 </script>
</body>

</html>
