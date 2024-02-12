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

$current_month = date('m');
$current_year = date('Y');
$sql = "SELECT * FROM admin_records WHERE MONTH(date_encoded) = '$current_month' AND YEAR(date_encoded) = '$current_year' ORDER BY requested_by DESC";
$result = $conn->query($sql);

if (mysqli_num_rows($result) > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

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

        // Add headers to the spreadsheet
        $columnIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
            $columnIndex++;
        }

        // Fetch data from admin_records table and populate the spreadsheet
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

        // Add totals row
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

        

        // Apply styles to the totals row
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

        // Apply auto size to columns
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save and download the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'MonthlyReport.xlsx';
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
?>
