<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CsvProcessor
{
    protected $category;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * Process CSV file with batch processing
     */
    public function processFile($filePath)
    {
        try {
            Log::info("Starting CSV processing for category: " . $this->category);

            // Read CSV file
            $data = $this->readCsvFile($filePath);
            Log::info("CSV file read successfully. Found " . count($data) . " rows");

            // Process in batches for better performance
            $batchSize = 100;
            $batches = array_chunk($data, $batchSize);

            foreach ($batches as $batchIndex => $batch) {
                Log::info("Processing batch " . ($batchIndex + 1) . " of " . count($batches));

                foreach ($batch as $rowIndex => $row) {
                    $globalIndex = ($batchIndex * $batchSize) + $rowIndex + 1;
                    $this->processRow($row, $globalIndex);
                }

                // Small delay to prevent memory issues
                usleep(10000); // 10ms delay
            }

            Log::info("CSV processing completed. Imported: {$this->importedCount}, Skipped: {$this->skippedCount}");

            return [
                'imported' => $this->importedCount,
                'skipped' => $this->skippedCount,
                'errors' => $this->errors
            ];

        } catch (\Exception $e) {
            Log::error("CSV processing failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Read CSV file
     */
    protected function readCsvFile($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Could not read CSV headers');
        }

        // Clean headers (remove BOM)
        $headers = array_map(function($header) {
            return trim($header, "\xEF\xBB\xBF");
        }, $headers);

        Log::info("CSV Headers: " . json_encode($headers));

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

    /**
     * Process a single row
     */
    protected function processRow($row, $rowNumber)
    {
        try {
            Log::info("Processing row #{$rowNumber}: " . json_encode($row));

            // Map field names
            $mappedRow = $this->mapFields($row);

            // Validate required fields - only name and email are required
            if (empty($mappedRow['name'])) {
                $this->skippedCount++;
                $this->errors[] = "Row #{$rowNumber}: Name is required";
                Log::warning("Row #{$rowNumber} skipped: Name is required");
                return;
            }

            // Email is required for lead import
            if (empty($mappedRow['email'])) {
                $this->skippedCount++;
                $this->errors[] = "Row #{$rowNumber}: Email is required";
                Log::warning("Row #{$rowNumber} skipped: Email is required");
                return;
            }

            // Check for duplicates (only check email since it's required)
            if ($this->isDuplicate($mappedRow['email'], $mappedRow['phone'])) {
                $this->skippedCount++;
                $this->errors[] = "Row #{$rowNumber}: Duplicate lead (email already exists)";
                Log::warning("Row #{$rowNumber} skipped: Duplicate lead");
                return;
            }

            // Create lead
            $this->createLead($mappedRow, $rowNumber);

        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->errors[] = "Row #{$rowNumber}: " . $e->getMessage();
            Log::error("Row #{$rowNumber} error: " . $e->getMessage());
        }
    }

    /**
     * Map CSV fields to database fields
     */
    protected function mapFields($row)
    {
        $mapped = [];

        // Direct mappings
        $mapped['name'] = $row['Name'] ?? $row['name'] ?? '';
        $mapped['email'] = $row['Email'] ?? $row['email'] ?? null;
        $mapped['phone'] = $row['Phone'] ?? $row['phone'] ?? null;
        $mapped['company'] = $row['Company'] ?? $row['company'] ?? null;
        $mapped['address'] = $row['Address'] ?? $row['address'] ?? null;
        $mapped['municipality'] = $row['Municipality'] ?? $row['municipality'] ?? null;
        $mapped['website'] = $row['Website'] ?? $row['website'] ?? null;
        $mapped['facebook'] = $row['Facebook'] ?? $row['facebook'] ?? null;
        $mapped['instagram'] = $row['Instagram'] ?? $row['instagram'] ?? null;
        $mapped['twitter'] = $row['Twitter'] ?? $row['twitter'] ?? null;
        $mapped['latitude'] = $row['Latitude'] ?? $row['latitude'] ?? null;
        $mapped['longitude'] = $row['Longitude'] ?? $row['longitude'] ?? null;
        $mapped['rating'] = $row['Rating'] ?? $row['rating'] ?? null;
        $mapped['review_count'] = $row['Review Count'] ?? $row['review_count'] ?? 0;
        $mapped['claimed'] = $row['Claimed'] ?? $row['claimed'] ?? false;
        $mapped['notes'] = $row['Notes'] ?? $row['notes'] ?? null;

        return $mapped;
    }

    /**
     * Check for duplicate leads (only check email since it's required)
     */
    protected function isDuplicate($email, $phone)
    {
        // Only check email since it's required for lead import
        if (empty($email)) {
            return false;
        }

        // Use a more efficient query with specific columns
        return Lead::select('id')
            ->where('email', $email)
            ->limit(1)
            ->exists();
    }

    /**
     * Create lead in database
     */
    protected function createLead($data, $rowNumber)
    {
        try {
            DB::beginTransaction();

            $leadData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => $data['company'],
                'category' => $this->category,
                'address' => $data['address'],
                'municipality' => $data['municipality'],
                'website' => $data['website'],
                'facebook' => $data['facebook'],
                'instagram' => $data['instagram'],
                'twitter' => $data['twitter'],
                'latitude' => $this->parseDecimal($data['latitude']),
                'longitude' => $this->parseDecimal($data['longitude']),
                'rating' => $this->parseDecimal($data['rating']),
                'review_count' => $this->parseInteger($data['review_count']),
                'claimed' => $this->parseBoolean($data['claimed']),
                'notes' => $data['notes'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];

            Log::info("Creating lead with data: " . json_encode($leadData));

            $lead = Lead::create($leadData);

            if (!$lead || !$lead->id) {
                throw new \Exception("Failed to create lead");
            }

            $this->importedCount++;
            Log::info("Lead created successfully with ID: " . $lead->id);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Parse decimal value
     */
    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return null;
        }
        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Parse integer value
     */
    protected function parseInteger($value)
    {
        if (empty($value)) {
            return 0; // Return 0 instead of null for required integer fields
        }
        return is_numeric($value) ? (int) $value : 0;
    }

    /**
     * Parse boolean value
     */
    protected function parseBoolean($value)
    {
        if (empty($value)) {
            return false;
        }
        $value = strtolower(trim($value));
        return in_array($value, ['true', '1', 'yes', 'y', 'on']);
    }

    /**
     * Get import statistics
     */
    public function getStats()
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors
        ];
    }
}
