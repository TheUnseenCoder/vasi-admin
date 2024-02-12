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
<head>
    <link rel="icon" href="<?php echo $logo_default; ?>">
</head>

