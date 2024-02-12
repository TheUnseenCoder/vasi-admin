<?php
include '../../conn.php';
$id = 7;
              $query = "SELECT category_name FROM admin_categories";
              $result = mysqli_query($conn, $query);

              // Check if there are any categories
              if (mysqli_num_rows($result) > 0) {
                  echo '<div class="row mb-2">';
                  $count = 0;

                  // Loop through each category
                  while ($row1 = mysqli_fetch_assoc($result)) {
                      $category_name = $row1['category_name'];
                      // Replace characters and convert to lowercase format
                      $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));

                      // Output a number input field for the category name

                          // Fetch value from the row if the column exists
                          $value_query = "SELECT $category_name_replace FROM admin_records WHERE record_id = '$id'";
                          $result1 = mysqli_query($conn, $value_query);
                          $row2 = mysqli_fetch_assoc($result1);
                          $value = $row2[$category_name_replace];
                          echo '<div class="col-md-4">';
                          echo '<label for="' . $category_name_replace . '">' . htmlspecialchars($category_name) . '</label>';
                          echo '<input type="number" name="' . $category_name_replace . '" class="form-control category_amount" value= "'. $value .'" id="' . $category_name_replace . '">';
                      echo '</div>';
                      $count++;
                      // If three inputs have been added, close the row and start a new one
                      if ($count % 3 == 0) {
                          echo '</div><div class="row mb-2">';
                      }
                  }
                  // Close the last row
                  echo '</div>';
              } else {
                  // If there are no categories
                  echo 'No categories found.';
              }
?>