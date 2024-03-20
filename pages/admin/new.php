<?php
// Example input
$input = "Velocity Advance Incorporated Solutions";

// Convert the input to lowercase for case-insensitive comparison
$input = strtolower($input);

// Initialize company type variable
$company_type = "";

// Check keywords using switch-case
switch (true) {
    case (
        strpos($input, "corp") !== false || 
        strpos($input, "inc") !== false || 
        strpos($input, "corporation") !== false || 
        strpos($input, "company") !== false || 
        strpos($input, "incorporated") !== false
    ):
        $company_type = "Corporation";
        break;
    case substr($input, -2) === "co":
    case substr($input, -3) === "co.":
    case substr($input, -3) === "ltd":
    case substr($input, -5) === "hotel":
        $company_type = "Corporation";
        break;
    default:
        $company_type = "Individual";
}

echo $company_type; // Output the determined company type


?>