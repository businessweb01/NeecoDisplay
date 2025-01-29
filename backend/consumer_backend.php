<?php
require '../vendor/autoload.php'; // Include the necessary PHP libraries for Excel file handling

use PhpOffice\PhpSpreadsheet\IOFactory;

$uploadDir = __DIR__ . '/../uploads/'; // Define the upload directory

// Scan the directory for .xlsx files
$files = glob($uploadDir . '*.xlsx');

// Check if any .xlsx file is found
if (count($files) > 0) {
    $excelFilePath = $files[0]; // Pick the first .xlsx file in the directory

    try {
        // Load the existing Excel file
        $spreadsheet = IOFactory::load($excelFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Prepare data array
        $data = [
            'labels' => [], // Column A (Row 7 onward)
            'dataset1' => [], // Column D
            'dataset2' => [], // Column J
            'dataset3' => [], // Column K
            'dataset4' => [], // Column L
            'dataset5' => [], // Column M
        ];

        $startRow = 7; // Start reading from row 7 (skip header)
        $maxRow = $sheet->getHighestRow(); // Get the highest row in the sheet

        // Count non-empty rows in Column A starting from Row 7
        $count = 0;
        for ($row = $startRow; $row <= $maxRow; $row++) {
            $valueA = $sheet->getCell("A$row")->getValue();
            if ($valueA !== null && $valueA !== '') {
                $count++;
            }
        }

        // Read the corresponding number of rows from Columns A, D, E, J, K, and L
        for ($row = $startRow; $row < $startRow + $count; $row++) {
            $label = $sheet->getCell("A$row")->getValue(); // Column A for labels
            $value1 = $sheet->getCell("B$row")->getValue(); // Column B
            $value2 = $sheet->getCell("C$row")->getValue(); // Column C
            $value3 = $sheet->getCell("D$row")->getValue(); // Column D
            $value4 = $sheet->getCell("E$row")->getValue(); // Column E 
            $value5 = $sheet->getCell("J$row")->getValue(); // Column J

            // Populate data array for charts
            if ($label !== null) {
                $data['labels'][] = $label;
                $data['dataset1'][] = $value1 !== null ? $value1 : 0;
                $data['dataset2'][] = $value2 !== null ? $value2 : 0;
                $data['dataset3'][] = $value3 !== null ? $value3 : 0;
                $data['dataset4'][] = $value4 !== null ? $value4 : 0;
                $data['dataset5'][] = $value5 !== null ? $value5 : 0;
            }
        }

        // Return the processed data as JSON
        header('Content-Type: application/json');
        echo json_encode($data);

    } catch (Exception $e) {
        // Handle errors (e.g., invalid file format or corrupt Excel file)
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error processing file: ' . $e->getMessage()]);
    }
} else {
    // Handle missing Excel file
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No Excel file found']);
}
