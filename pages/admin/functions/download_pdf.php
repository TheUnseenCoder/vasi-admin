<?php
session_start();

require('../../../fpdf.php');
date_default_timezone_set('Asia/Manila');
// MySQL database configuration
$host = 'localhost';
$username = 'nuqkqixl_adminadmin';
$password = '8QM6?QiVTZ+]';
$database = 'nuqkqixl_inventorymanagement';

// Connect to MySQL database
$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$sql1 = "SELECT * FROM admin_designs";
if($rs1=$mysqli->query($sql1)){
    while ($row1=$rs1->fetch_assoc()) {
        $logo=$row1['logo'];
        $title=$row1['title'];
    }
}

class PDF extends FPDF
{
    private $logo; // Private property to store the logo image
    private $title; // Private property to store the title
    private $mysqli; // Private property to store the MySQL connection

    // Constructor that accepts the logo image, title, and MySQL connection as parameters
    public function __construct($logo, $title, $mysqli)
    {
        parent::__construct();
        $this->logo = $logo; // Assign the logo image to the property
        $this->title = $title; // Assign the title to the property
        $this->mysqli = $mysqli; // Assign the MySQL connection to the property
    }

    // Header
    function Header()
    {
        $this->Image($this->logo, 70, 8, 15);
        
        $this->SetFont('Arial','B',18);
        $this->Cell(0, 10, $this->title . ' Inventory Management System', 0, 1, 'C');
        $this->Ln(5);
    }
    function Body() {
        // Table header
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, 6, "", 1, 0, 'C');
        $this->Cell(35, 6, "Serial ID", 1, 0, 'C');
        $this->Cell(40, 6, "Product Name", 1, 0, 'C');
        $this->Cell(60, 6, "Description", 1, 0, 'C');
        $this->Cell(25, 6, "Category", 1, 0, 'C');
        $this->Cell(25, 6, "Brand", 1, 0, 'C');
        $this->Cell(25, 6, "Status", 1, 0, 'C');
        $this->Cell(55, 6, "Last Updated", 1, 0, 'C');
        $this->Ln();

        $sql = "SELECT * FROM admin_products WHERE product_id is not NULL ORDER BY last_updated DESC";
        if ($rs = $this->mysqli->query($sql)) {
            $i = 0;
            while ($row = $rs->fetch_assoc()) {
                $i++;
                $product_id = $row['product_id'];
                $product_name = $row['product_name'];
                $product_description = $row['product_description'];
                $category = $row['category'];
                $brand = $row['brand'];
                $date = new DateTime($row['last_updated']);
                $formattedDate = $date->format('F j, Y' . ' - ' . 'h:i A');
                $status = $row['status'];
                
                $this->SetFont('Arial', '', 10);
                // Output table data
                $this->Cell(15, 6, $i, 1, 0, 'C');
                $this->Cell(35, 6, $product_id, 1, 0, 'C');
                $this->Cell(40, 6, $product_name, 1, 0, 'C');
                // Set the initial font size
                $fontSize = 10;
                
                // Set the maximum width for the cell
                $maxWidth = 60;
                
                // Loop until the content fits within the cell width
                while ($this->GetStringWidth($product_description) > $maxWidth) {
                    // Decrease the font size
                    $fontSize--;
                
                    // Set the font with the updated size
                    $this->SetFont('Arial', '', $fontSize);
                }
                
                // Output the cell with the adjusted font size
                $this->Cell($maxWidth, 6, $product_description, 1, 0, 'C');
                
                // Reset the font to its original size if needed
                // Set the initial font size
                $fontSize = 10;
                
                // Set the maximum width for each cell
                $maxWidth = 25;
                
                // Loop for $category
                while ($this->GetStringWidth($category) > $maxWidth) {
                    $fontSize--;
                    $this->SetFont('Arial', '', $fontSize);
                }
                
                // Output the $category cell with the adjusted font size
                $this->Cell($maxWidth, 6, $category, 1, 0, 'C');
                
                // Reset the font to its original size if needed
                $this->SetFont('Arial', '', 10);
                
                // Loop for $brand
                while ($this->GetStringWidth($brand) > $maxWidth) {
                    $fontSize--;
                    $this->SetFont('Arial', '', $fontSize);
                }
                
                // Output the $brand cell with the adjusted font size
                $this->Cell($maxWidth, 6, $brand, 1, 0, 'C');
                
                // Reset the font to its original size if needed
                $this->SetFont('Arial', '', 10);
                $this->Cell(25, 6, $status, 1, 0, 'C');
                $this->Cell(55, 6, $formattedDate, 1, 0, 'C');
                $this->Ln(); // Move to the next row
            }
        }
    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
            
        $this->SetFont('Arial', '', 8);
    
        // Width = 0 means the cell is extended up to the right margin
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . " / {pages}", 0, 0, 'C');
    }
}

$pdf = new PDF($logo, $title, $mysqli); // Pass the logo image and title to the PDF class constructor

// Define page numbering
$pdf->AliasNbPages('{pages}');

// Set auto page break and add the first page
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage('L');

// Call the Body function to generate the table
$pdf->Body();

// Set the title and author of the PDF
$pdf->SetTitle($title);
$pdf->SetAuthor($title);

// Close MySQL connection
$mysqli->close();

// Output the PDF
$pdf->Output();
?>