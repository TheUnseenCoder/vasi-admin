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
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$from_date_formatted = date('F Y', strtotime($from_date));

$sql = "SELECT * FROM admin_record_details";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();


    $sheet->setCellValue('A1', 'Company Name');
    $sheet->setCellValue('B1', 'VELOCITY ADVANCED SOLUTIONS, INC.');
    $sheet->setCellValue('A2', 'Address');
    $sheet->setCellValue('B2', '428 BAELLO BLDG. A. MABINI STREET, BARANGAY 015, CALOOCAN CITY, 1400');
    $sheet->setCellValue('A3', 'Name of Owner');
    $sheet->setCellValue('B3', 'MR. DENNIS BAELLO');
    $sheet->setCellValue('A4', 'Tin');
    $sheet->setCellValue('B4', '009-929-652-00000');
    $sheet->setCellValue('A5', '');
    $sheet->setCellValue('A6', 'Schedule of Expenses');
    $sheet->setCellValue('A7', 'FOR THE MONTH OF');
    $sheet->setCellValue('B7', $from_date_formatted);
    $sheet->setCellValue('A8', '');
    $sheet->getStyle('A1:B8')->applyFromArray([
        'font' => ['bold' => true, 'size' => 10, 'name' => 'Tahoma'],
    ]);


    $sheet->getStyle('A9:M9')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Tahoma'],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    $headers = [
        'Transaction Date',
        'Supplier Name',
        'Address',
        'TIN',
        'Company Type',
        'Document Type',
        'Doc Number',
        'V/NV',
        'Amount',
        'Account Title',
        'Goods/Service/Others',
        'Particulars',
        'Mode of Payment',
    ];

    $sheet->fromArray([$headers], NULL, 'A9');

    $rowIndex = 10;

    while ($row = $result->fetch_assoc()) {
        $transaction_dates = explode(', ', $row['transaction_date']);
        $category_names = explode(', ', $row['category_names']);
        $category_amounts = explode(', ', $row['category_amounts']);
        $supplier_names = explode(', ', $row['supplier_name']);
        $addresses = explode(', ', $row['address']);
        $tins = explode(', ', $row['tin']);
        $doc_types = explode(', ', $row['doc_type']);
        $doc_nums = explode(', ', $row['doc_num']);
        $goods_service_others = explode(', ', $row['goods_service_others']);
        $particulars = explode(', ', $row['particulars']);
        $company_types = explode(', ', $row['company_types']);
        $v_nv = explode(', ', $row['v_nv']);

        foreach ($transaction_dates as $index => $date) {
            $date1 = date('j-M', strtotime($date));

            if (strtotime($date) >= strtotime($from_date) && strtotime($date) <= strtotime($to_date)) {

                $category_name = '';
                $categoryQuery = "SELECT category_name FROM admin_categories WHERE category_change = '" . $category_names[$index] . "'";
                $categoryResult = $conn->query($categoryQuery);
                if ($categoryResult && $categoryResult->num_rows > 0) {
                    $categoryRow = $categoryResult->fetch_assoc();
                    $category_name = $categoryRow['category_name'];
                }
    
                $rowData = [
                    $date1,
                    $supplier_names[$index] ?? '',
                    $addresses[$index] ?? '',
                    $tins[$index] ?? '',
                    $company_types[$index] ?? '',
                    $doc_types[$index] ?? '',
                    $doc_nums[$index] ?? '',
                    $v_nv[$index] ?? '',
                    $category_amounts[$index] ?? '',
                    $category_name, 
                    $goods_service_others[$index] ?? '',
                    $particulars[$index] ?? '',
                ];
                $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
                // Adjust alignment for specific columns
                $centerAlignedColumns = ['A', 'D', 'F', 'G', 'H', 'M'];
                foreach ($centerAlignedColumns as $column) {
                    $sheet->getStyle($column . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $rowIndex++;
            }
        }
    }

$totalAmount = 0;
$totalRowIndex = $rowIndex;


for ($row = 10; $row < $rowIndex; $row++) {
    $amountCell = 'I' . $row;
    $amount = $sheet->getCell($amountCell)->getValue();

    if (is_numeric($amount)) {
        $totalAmount += $amount;
    } else {
        $totalRowIndex = $row;
        break;
    }
}

$formattedTotalAmount = number_format($totalAmount, 2);

    for ($i = 0; $i < 5; $i++) {
        $rowIndex++;
        $sheet->fromArray([[]], NULL, 'A' . $rowIndex);
    }

    $totalRowIndex = $rowIndex - 4;
    $sheet->setCellValue('A' . $totalRowIndex, 'TOTAL');
    $sheet->setCellValue('I' . $totalRowIndex, $formattedTotalAmount);

    $sheet->getStyle('A' . $totalRowIndex . ':M' . $totalRowIndex)->applyFromArray([
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
        'font' => [
            'bold' => true,
        ],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    $lastRowIndex = $totalRowIndex - 1; 
    $sheet->getStyle('I10:I' . $lastRowIndex)->getNumberFormat()->setFormatCode('#,##0.00');

    foreach (range('A', 'M') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Center align header cells
    foreach(range('A', 'M') as $column) {
        $sheet->getStyle($column . '9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    $sheet->freezePane('B10');

    $sheet->getStyle('A1:M' . $rowIndex)->applyFromArray([
        'font' => ['size' => 10, 'name' => 'Tahoma'],
    ]);


    $writer = new Xlsx($spreadsheet);
    $filename = 'Expense_Summary_' . $from_date_formatted. '.xlsx';
    $writer->save($filename);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Cache-Control: max-age=0");

    readfile($filename);

    unlink($filename);

    $conn->close();

} else {
    echo "No data found for the selected date range.";
}
?>
