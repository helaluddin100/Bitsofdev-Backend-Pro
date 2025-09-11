<?php

namespace App\Imports;

class ExcelReader
{
    /**
     * Convert Excel file to CSV format
     */
    public static function convertToCsv($filePath)
    {
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($fileExtension === 'csv') {
            return $filePath; // Already CSV
        }

        if (!in_array($fileExtension, ['xlsx', 'xls'])) {
            throw new \Exception('Unsupported file format. Please use CSV, XLSX, or XLS files.');
        }

        // For now, we'll use a simple approach
        // In production, you might want to use a proper Excel library
        throw new \Exception('Excel files are not supported yet. Please save your file as CSV format.');
    }

    /**
     * Read Excel file and return data as array
     */
    public static function readExcel($filePath)
    {
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Always try to read as CSV first (works for both CSV and sometimes Excel)
        try {
            return self::readCsv($filePath);
        } catch (\Exception $e) {
            // If it's an Excel file and CSV reading failed, provide instructions
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                throw new \Exception('Excel files (.xlsx, .xls) are not supported yet. Please convert your Excel file to CSV format first. To convert: 1) Open Excel file, 2) Go to File → Save As, 3) Choose CSV (Comma delimited) format, 4) Save and upload the CSV file.');
            }

            // For other file types
            if (!in_array($fileExtension, ['csv', 'xlsx', 'xls'])) {
                throw new \Exception('Unsupported file format. Please use CSV, XLSX, or XLS files.');
            }

            // Re-throw the original error for CSV files
            throw $e;
        }
    }

    /**
     * Read CSV file
     */
    private static function readCsv($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \Exception('Could not open file for reading');
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Could not read CSV headers');
        }

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Ensure row has same number of elements as headers
            $rowCount = count($row);
            $headerCount = count($headers);

            if ($rowCount < $headerCount) {
                $row = array_pad($row, $headerCount, '');
            } elseif ($rowCount > $headerCount) {
                $row = array_slice($row, 0, $headerCount);
            }

            $data[] = array_combine($headers, $row);
        }

        fclose($handle);
        return $data;
    }
}
