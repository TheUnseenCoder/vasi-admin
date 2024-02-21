<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Manila');
// MySQL database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'vasi-admin';

// Connect to MySQL database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

// Convert from_date to the desired format
$from_date_formatted = date('Md,Y', strtotime($from_date));

// Convert to_date to the desired format
$to_date_formatted = date('Md,Y', strtotime($to_date));

$sql = "SELECT * FROM admin_records 
        WHERE DATE(date_encoded) BETWEEN '$from_date' AND '$to_date'  
        ORDER BY requested_by DESC";
$result = $conn->query($sql);

if (mysqli_num_rows($result) > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $title = $from_date_formatted . '-' . $to_date_formatted;
    $sheet->setTitle($title);
    // Fetch categories from admin_categories table
    $query = "SELECT category_name FROM admin_categories";
    $category_result = mysqli_query($conn, $query);

    // Check if there are any categories
    if (mysqli_num_rows($category_result) > 0) {
        $headers = array(
            'requested_by' => 'Requested By',
            'purpose' => 'Purpose',
            'project_site' => 'Project/Site',
            'amount' => 'Amount',
            'returned_cash' => 'Returned Cash',
            'date_encoded' => 'Date Encoded',
        );

        // Add category names to the headers array
        while ($row = mysqli_fetch_assoc($category_result)) {
            $category_name = $row['category_name'];
            $category_name_replace = strtolower(str_replace([' - ', ', ', ' / ', '-', ',','/',' '], '_', $category_name));
            $headers[$category_name_replace] = $category_name;
        }

        // Add headers to the first sheet
        $columnIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
            $columnIndex++;
        }

        // Fetch data from admin_records table and populate the first sheet
        $rowIndex = 2;
        $prevRequestedBy = '';

        // Initialize totals
        $totals = array_fill_keys(array_values($headers), 0);
        $amountTotal = 0;
        $returnedCashTotal = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $columnIndex = 1;
            foreach ($headers as $key => $header) {
                // Set the value for each field
                if ($key === 'record_id') {
                    $value = $row['record_id'];
                } elseif ($key === 'date_encoded') {
                    $date = new DateTime($row[$key]);
                    $value = $date->format('M j, Y');
                } elseif ($key === 'requested_by' || $key === 'purpose' || $key === 'project_site') {
                    $value = $row[$key];
                } else {
                    $category_name = array_search($header, $headers);
                    $category_value_query = "SELECT $category_name FROM admin_records WHERE record_id = {$row['record_id']}";
                    $category_value_result = $conn->query($category_value_query);
                    if ($category_value_result && $category_value_row = $category_value_result->fetch_assoc()) {
                        $value = doubleval($category_value_row[$category_name]);
                        // Calculate totals
                        if ($key === 'amount') {
                            $amountTotal += $value;
                        } elseif ($key === 'returned_cash') {
                            $returnedCashTotal += $value;
                        } else {
                            $totals[$header] += $value;
                        }
                    } else {
                        $value = '';
                    }
                }

                // Set the cell value only if it's the first occurrence of the requested_by value
                if ($key !== 'requested_by' || $prevRequestedBy !== $value) {
                    // Check if the value is numeric and set it as a number if it is
                    if (!is_numeric($value)) {
                        $sheet->setCellValueExplicitByColumnAndRow($columnIndex, $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                    }
                }

                $columnIndex++;
            }

            // Update the previous requested_by value
            $prevRequestedBy = $row['requested_by'];

            // Increment the row index
            $rowIndex++;
        }

        // Add totals row to the first sheet
        $sheet->setCellValue('A' . $rowIndex, 'Total');
        $columnIndex = 2; // Start at the first index
        foreach ($headers as $key => $header) {
            if ($key !== 'record_id' && $key !== 'date_encoded' && $key !== 'requested_by') {
                // Check if it's the 4th index, if so, add a column before it
                if ($columnIndex === 6) {
                    $columnIndex++;
                }
                $total = ($key === 'amount') ? $amountTotal : (($key === 'returned_cash') ? $returnedCashTotal : $totals[$header]);
                if ($total == 0) {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, '');
                } else {
                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $total);
                }
                $columnIndex++;
            }
        }

        // Apply styles to the totals row of the first sheet
        $lastColumn = $sheet->getHighestColumn();
        $style = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ),
            'font' => array(
                'bold' => true
            )
        );
        $sheet->getStyle('A' . $rowIndex . ':' . $lastColumn . $rowIndex)->applyFromArray($style);

        // Apply auto size to columns of the first sheet
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Call the function to populate the second sheet
        $spreadsheet = populateSecondSheet($spreadsheet, $from_date, $to_date, $conn, $headers);

        // Save and download the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'MonthlyReport_from_' . $from_date . '_to_' . $to_date . '.xlsx';
        $writer->save($filename);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Cache-Control: max-age=0");

        readfile($filename);

        unlink($filename);

        $conn->close();
    } else {
        // If there are no categories
        exit("No categories found.");
    }
} else {
    // If there is no data to export
    exit("No data to export.");
}

function populateSecondSheet($spreadsheet, $from_date, $to_date, $conn, $headers) {
    // Create a new sheet
    $newSheet = $spreadsheet->createSheet();
    // Convert from_date to the desired format
    $from_date_formatted = date('Md,Y', strtotime($from_date));

    // Convert to_date to the desired format
    $to_date_formatted = date('Md,Y', strtotime($to_date));

    $title = "Summary-".$from_date_formatted."-".$to_date_formatted;
    $newSheet->setTitle($title);

    // Fetch unique employee names from the admin_records table
    $sql = "SELECT DISTINCT requested_by FROM admin_records 
            WHERE DATE(date_encoded) BETWEEN '$from_date' AND '$to_date'  
            ORDER BY requested_by ASC";
    $result = $conn->query($sql);

    // Check if there is data
    if ($result && $result->num_rows > 0) {
        // Initialize the row index
        $rowIndex = 1;

        // Add headers to the second sheet
        $newSheet->setCellValue('A1', 'Category');
        $columnIndex = 2;
        foreach ($result as $row) {
            $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $row['requested_by']);
            $columnIndex++;
        }
        $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, 'Total'); // Add title for the Total column

        // Initialize arrays to store column totals and track non-zero columns
        $columnTotals = array_fill(2, count($headers) - 1, 0);
        $nonZeroColumns = array_fill(2, count($headers) - 1, false);

        // Fetch data for each category
        foreach ($headers as $category => $header) {
            // Skip non-category columns and specified columns
            if ($category === 'requested_by' || $category === 'date_encoded' || $category === 'purpose' || $category === 'project_site' || $category === 'amount' || $category === 'returned_cash') {
                continue;
            }

            // Add category name to the first column
            $newSheet->setCellValue('A' . ($rowIndex + 1), $header);

            // Fetch data for each employee
            $columnIndex = 2;
            $categoryTotal = 0; // Initialize category total
            foreach ($result as $row) {
                $requested_by = $row['requested_by'];

                // Fetch total amount for the current category and employee
                $sql = "SELECT SUM($category) AS total FROM admin_records 
                        WHERE requested_by = '$requested_by' AND DATE(date_encoded) BETWEEN '$from_date' AND '$to_date'";
                $totalResult = $conn->query($sql);
                $total = $totalResult->fetch_assoc()['total'];

                // Add total amount to the corresponding cell
                $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex + 1, $total);

                // Add to column total
                $columnTotals[$columnIndex] += $total;

                // Add to category total
                $categoryTotal += $total;

                // Track non-zero columns
                if ($total != 0) {
                    $nonZeroColumns[$columnIndex] = true;
                }

                $columnIndex++;
            }

            // Set the total for the current category (including zeros)
            $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex + 1, $categoryTotal);

            // Move to the next row for the next category
            $rowIndex++;
        }

        // Calculate the total for each non-zero column
        $newSheet->setCellValue('A' . ($rowIndex + 1), 'Total');
        $columnIndex = 2;
        foreach ($columnTotals as $index => $total) {
            if ($nonZeroColumns[$index]) {
                $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex + 1, $total);
            } else {
                $newSheet->setCellValueByColumnAndRow($columnIndex, $rowIndex + 1, '');
            }
            $columnIndex++;
        }

        // Find the empty cell in the total row and insert the total sum
        $emptyCell = array_search('', $columnTotals);
        if ($emptyCell !== false) {
            $totalSum = array_sum(array_filter($columnTotals, function($value) {
                return is_numeric($value);
            }));
            $newSheet->setCellValueByColumnAndRow($emptyCell, $rowIndex + 1, $totalSum);
        }
    } else {
        // If no data is found, add a message
        $newSheet->setCellValue('A1', 'No data found');
    }

    return $spreadsheet;
}


?>
