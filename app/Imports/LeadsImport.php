<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadsImport
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
     * Process the CSV data
     */
    public function processCsvData($data)
    {
        foreach ($data as $row) {
            $this->processRow($row);
        }
    }

    /**
     * Map field names to standard format
     */
    protected function mapFieldNames($row)
    {
        $fieldMappings = [
            'Name' => 'name',
            'name' => 'name',
            'Email' => 'email',
            'email' => 'email',
            'Phone' => 'phone',
            'phone' => 'phone',
            'Fulladdress' => 'full_address',
            'full_address' => 'full_address',
            'Street' => 'street',
            'street' => 'street',
            'Municipality' => 'municipality',
            'municipality' => 'municipality',
            'Categories' => 'categories',
            'categories' => 'categories',
            'Phones' => 'phones',
            'phones' => 'phones',
            'Claimed' => 'claimed',
            'claimed' => 'claimed',
            'Review Count' => 'review_count',
            'review_count' => 'review_count',
            'Average Rating' => 'average_rating',
            'average_rating' => 'average_rating',
            'Review URL' => 'review_url',
            'review_url' => 'review_url',
            'Google Maps URL' => 'google_maps_url',
            'google_maps_url' => 'google_maps_url',
            'Latitude' => 'latitude',
            'latitude' => 'latitude',
            'Longitude' => 'longitude',
            'longitude' => 'longitude',
            'Website' => 'website',
            'website' => 'website',
            'Domain' => 'domain',
            'domain' => 'domain',
            'Opening hours' => 'opening_hours',
            'opening_hours' => 'opening_hours',
            'Featured image' => 'featured_image',
            'featured_image' => 'featured_image',
            'Cid' => 'cid',
            'cid' => 'cid',
            'Place Id' => 'place_id',
            'place_id' => 'place_id',
            'Kgmid' => 'kgmid',
            'kgmid' => 'kgmid',
            'Plus code' => 'plus_code',
            'plus_code' => 'plus_code',
            'Google Knowledge URL' => 'google_knowledge_url',
            'google_knowledge_url' => 'google_knowledge_url',
            'Social Medias' => 'social_medias',
            'social_medias' => 'social_medias',
            'Facebook' => 'facebook',
            'facebook' => 'facebook',
            'Instagram' => 'instagram',
            'instagram' => 'instagram',
            'Twitter' => 'twitter',
            'twitter' => 'twitter',
            'Yelp' => 'yelp',
            'yelp' => 'yelp',
        ];

        $mappedRow = [];
        foreach ($row as $key => $value) {
            $mappedKey = $fieldMappings[$key] ?? $key;
            $mappedRow[$mappedKey] = $value;
        }

        return $mappedRow;
    }

    /**
     * Process a single row
     */
    protected function processRow($row)
    {
        try {
            Log::info("Processing row: " . json_encode($row));

            // Clean BOM characters from field names
            $cleanedRow = [];
            foreach ($row as $key => $value) {
                $cleanKey = trim($key, "\xEF\xBB\xBF"); // Remove BOM
                $cleanedRow[$cleanKey] = $value;
            }

            // Map common field variations
            $mappedRow = $this->mapFieldNames($cleanedRow);

            // Validate required fields
            $validator = Validator::make($mappedRow, [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                $this->skippedCount++;
                $errorMsg = "Row skipped: " . implode(', ', $validator->errors()->all());
                $this->errors[] = $errorMsg;
                Log::warning("Validation failed: " . $errorMsg);
                return;
            }

            // Use mapped row for processing
            $row = $mappedRow;

            // Check for duplicates
            if (Lead::isDuplicate($row['email'] ?? null, $row['phone'] ?? null)) {
                $this->skippedCount++;
                $errorMsg = "Duplicate lead skipped: {$row['name']}";
                $this->errors[] = $errorMsg;
                Log::info($errorMsg);
                return;
            }

            // Prepare lead data
            $leadData = [
                'name' => $row['name'],
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'phones' => $this->parsePhones($row['phones'] ?? null),
                'company' => $row['company'] ?? null,
                'category' => $this->category,
                'address' => $row['address'] ?? null,
                'full_address' => $row['full_address'] ?? $row['address'] ?? null,
                'street' => $row['street'] ?? null,
                'municipality' => $row['municipality'] ?? null,
                'website' => $row['website'] ?? null,
                'domain' => $row['domain'] ?? null,
                'facebook' => $row['facebook'] ?? null,
                'instagram' => $row['instagram'] ?? null,
                'twitter' => $row['twitter'] ?? null,
                'yelp' => $row['yelp'] ?? null,
                'latitude' => $this->parseDecimal($row['latitude'] ?? null),
                'longitude' => $this->parseDecimal($row['longitude'] ?? null),
                'rating' => $this->parseDecimal($row['rating'] ?? null),
                'review_count' => $this->parseInteger($row['review_count'] ?? null),
                'claimed' => $this->parseBoolean($row['claimed'] ?? null),
                'notes' => $row['notes'] ?? null,
                'is_active' => true
            ];

            Log::info("Lead data prepared: " . json_encode($leadData));

            // Create lead
            $lead = Lead::create($leadData);
            $this->importedCount++;
            Log::info("Lead created successfully with ID: " . $lead->id);

            // Verify the lead was actually saved
            $savedLead = Lead::find($lead->id);
            if (!$savedLead) {
                throw new \Exception("Lead was not saved to database");
            }

        } catch (\Exception $e) {
            $this->skippedCount++;
            $errorMsg = "Error processing row: " . $e->getMessage();
            $this->errors[] = $errorMsg;
            Log::error("Lead import error: " . $e->getMessage() . " - Row: " . json_encode($row));
        }
    }

    /**
     * Parse phones from JSON string or comma-separated string
     */
    protected function parsePhones($phones)
    {
        if (empty($phones)) {
            return null;
        }

        // Try to decode as JSON first
        $decoded = json_decode($phones, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Parse as comma-separated string
        $phoneArray = array_map('trim', explode(',', $phones));
        return array_filter($phoneArray);
    }

    /**
     * Parse decimal value
     */
    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return null;
        }

        $decimal = (float) $value;
        return $decimal > 0 ? $decimal : null;
    }

    /**
     * Parse integer value
     */
    protected function parseInteger($value)
    {
        if (empty($value)) {
            return 0;
        }

        return (int) $value;
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
        return in_array($value, ['true', '1', 'yes', 'on']);
    }

    /**
     * Get imported count
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Process file (CSV or Excel)
     */
    public function processFile($filePath)
    {
        // Test database connection
        try {
            $testLead = Lead::count();
            Log::info("Database connection test - Current lead count: " . $testLead);
        } catch (\Exception $e) {
            Log::error("Database connection failed: " . $e->getMessage());
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }

        try {
            // Try to read the file using ExcelReader
            $data = ExcelReader::readExcel($filePath);

            Log::info("File read successfully. Found " . count($data) . " rows of data.");

            // Process each row
            foreach ($data as $index => $row) {
                $rowNumber = $index + 1;
                Log::info("Processing row #{$rowNumber}: " . json_encode($row));

                try {
                    $this->processRow($row);
                } catch (\Exception $e) {
                    $this->skippedCount++;
                    $this->errors[] = "Error processing row #{$rowNumber}: " . $e->getMessage();
                    Log::error("Lead import row error #{$rowNumber}: " . $e->getMessage() . " - Row data: " . json_encode($row));
                }
            }

            Log::info("Import completed. Processed " . count($data) . " rows total.");

        } catch (\Exception $e) {
            Log::error("File processing failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process CSV file (legacy method)
     */
    public function processCsvFile($filePath)
    {
        return $this->processFile($filePath);
    }
}
