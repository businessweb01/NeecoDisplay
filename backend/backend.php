<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__.'/../uploads/';
    $filePath = $uploadDir . basename($_FILES['file']['name']);

    // Move uploaded file to uploads folder
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        try {
            // Load the uploaded Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $data = [
                'labels' => [], // Column E (Row 7 onward)
                'dataset1' => [], // Column D
                'dataset2' => [], // Column J
                'dataset3' => [], // Column K
                'dataset4' => [], // Column L
                'dataset5' => [], // Column M
            ];

            $startRow = 7; // Start reading from row 7
            $maxRow = $sheet->getHighestRow(); // Get the highest row in the sheet

            // Count non-empty rows in Column A starting from Row 7
            $count = 0;
            for ($row = $startRow; $row <= $maxRow; $row++) {
                $valueA = $sheet->getCell("A$row")->getValue();
                if ($valueA !== null && $valueA !== '') {
                    $count++;
                }
            }

            // Read the corresponding number of rows from Columns D, E, J, and K
            for ($row = $startRow; $row < $startRow + $count; $row++) {
                $label = $sheet->getCell("A$row")->getValue(); // Column A for labels
                $value1 = $sheet->getCell("B$row")->getValue(); // Column B
                $value2 = $sheet->getCell("C$row")->getValue(); // Column C
                $value3 = $sheet->getCell("D$row")->getValue(); // Column D
                $value4 = $sheet->getCell("E$row")->getValue(); // Column E 
                $value5 = $sheet->getCell("J$row")->getValue(); // Column J

                if ($label !== null) {
                    $data['labels'][] = $label;
                    $data['dataset1'][] = $value1 !== null ? $value1 : 0;
                    $data['dataset2'][] = $value2 !== null ? $value2 : 0;
                    $data['dataset3'][] = $value3 !== null ? $value3 : 0;
                    $data['dataset4'][] = $value4 !== null ? $value4 : 0;
                    $data['dataset5'][] = $value5 !== null ? $value5 : 0;
                }
            }

            // Return data as JSON
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error processing file: ' . $e->getMessage()]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'File upload failed']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
}
?>
