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

            // Email is required for lead import (but allow if name exists and we can use a placeholder)
            // For businesses, sometimes email might not be available, so we'll generate a placeholder
            if (empty($mappedRow['email'])) {
                // Generate a placeholder email if name exists
                if (!empty($mappedRow['name'])) {
                    $mappedRow['email'] = strtolower(str_replace(' ', '.', preg_replace('/[^a-zA-Z0-9\s]/', '', $mappedRow['name']))) . '@imported.local';
                    Log::info("Row #{$rowNumber}: Generated placeholder email: " . $mappedRow['email']);
                } else {
                    $this->skippedCount++;
                    $this->errors[] = "Row #{$rowNumber}: Email is required and name is missing";
                    Log::warning("Row #{$rowNumber} skipped: Email and name are both required");
                    return;
                }
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

        // Map name - support multiple formats
        $mapped['name'] = $this->getFieldValue($row, [
            'Name', 'name', 
            // Combine First Name + Last Name if Name doesn't exist
        ]);
        
        // If name is empty, try to combine First Name and Last Name
        if (empty($mapped['name'])) {
            $firstName = $this->getFieldValue($row, ['First Name', 'first_name', 'First name', 'first name']);
            $lastName = $this->getFieldValue($row, ['Last Name', 'last_name', 'Last name', 'last name']);
            $mapped['name'] = trim($firstName . ' ' . $lastName);
        }

        // Map email - support multiple formats
        $mapped['email'] = $this->getFieldValue($row, [
            'Email', 'email', 'E-mail', 'e-mail', 'Email Address', 'email_address'
        ]);

        // Map phone - support multiple formats
        $mapped['phone'] = $this->getFieldValue($row, [
            'Phone', 'phone', 'Phone Number', 'phone_number', 'Phone Number', 'Phones'
        ]);

        // Map company - support multiple formats
        $mapped['company'] = $this->getFieldValue($row, [
            'Company', 'company', 'Company Name', 'company_name', 'Company Name'
        ]);

        // Map address - support multiple formats
        $mapped['address'] = $this->getFieldValue($row, [
            'Address', 'address', 'Fulladdress', 'Full Address', 'full_address', 'Fulladdress'
        ]);

        // Map full_address
        $mapped['full_address'] = $this->getFieldValue($row, [
            'Fulladdress', 'Full Address', 'full_address', 'Fulladdress', 'Address', 'address'
        ]);

        // Map street
        $mapped['street'] = $this->getFieldValue($row, [
            'Street', 'street', 'Street Name', 'street_name'
        ]);

        // Map municipality - support multiple formats
        $mapped['municipality'] = $this->getFieldValue($row, [
            'Municipality', 'municipality', 'City', 'city', 'Location', 'location'
        ]);

        // Map website
        $mapped['website'] = $this->getFieldValue($row, [
            'Website', 'website', 'Web', 'web', 'URL', 'url'
        ]);

        // Map domain
        $mapped['domain'] = $this->getFieldValue($row, [
            'Domain', 'domain'
        ]);

        // Map social media
        $mapped['facebook'] = $this->getFieldValue($row, [
            'Facebook', 'facebook', 'FB', 'fb'
        ]);

        $mapped['instagram'] = $this->getFieldValue($row, [
            'Instagram', 'instagram', 'IG', 'ig'
        ]);

        $mapped['twitter'] = $this->getFieldValue($row, [
            'Twitter', 'twitter', 'TW', 'tw'
        ]);

        // Map coordinates
        $mapped['latitude'] = $this->getFieldValue($row, [
            'Latitude', 'latitude', 'Lat', 'lat'
        ]);

        $mapped['longitude'] = $this->getFieldValue($row, [
            'Longitude', 'longitude', 'Lng', 'lng', 'Long', 'long'
        ]);

        // Map rating
        $mapped['rating'] = $this->getFieldValue($row, [
            'Rating', 'rating', 'Average Rating', 'average_rating', 'Average Rating'
        ]);

        // Map review count
        $mapped['review_count'] = $this->getFieldValue($row, [
            'Review Count', 'review_count', 'Review Count', 'Reviews', 'reviews', 'I: Review Count'
        ], 0);

        // Map claimed
        $mapped['claimed'] = $this->getFieldValue($row, [
            'Claimed', 'claimed', 'H: Claimed', 'Is Claimed', 'is_claimed'
        ], false);

        // Map notes
        $mapped['notes'] = $this->getFieldValue($row, [
            'Notes', 'notes', 'Note', 'note', 'Description', 'description'
        ]);

        // Map phones (JSON array) - if phones column exists
        $phonesValue = $this->getFieldValue($row, ['Phones', 'phones', 'G: Phones']);
        if (!empty($phonesValue)) {
            // Try to decode if it's JSON, otherwise treat as string
            $decoded = json_decode($phonesValue, true);
            $mapped['phones'] = is_array($decoded) ? $decoded : [$phonesValue];
        }

        // Map additional fields from screenshot
        $mapped['average_rating'] = $this->getFieldValue($row, [
            'Average Rating', 'average_rating', 'J: Average Rating', 'Rating', 'rating'
        ]);

        $mapped['review_url'] = $this->getFieldValue($row, [
            'Review URL', 'review_url', 'K: Review URL', 'Review Url', 'review_url'
        ]);

        $mapped['google_maps_url'] = $this->getFieldValue($row, [
            'Google Maps Url', 'google_maps_url', 'L: Google Maps Url', 'Google Maps URL', 'google_maps_url'
        ]);

        $mapped['opening_hours'] = $this->getFieldValue($row, [
            'Opening hours', 'opening_hours', 'Q: Opening hours', 'Opening Hours', 'opening_hours'
        ]);

        $mapped['featured_image'] = $this->getFieldValue($row, [
            'Featured image', 'featured_image', 'R: Featured image', 'Featured Image', 'featured_image'
        ]);

        $mapped['cid'] = $this->getFieldValue($row, [
            'Cid', 'cid', 'S: Cid', 'CID', 'cid'
        ]);

        $mapped['place_id'] = $this->getFieldValue($row, [
            'Place Id', 'place_id', 'T: Place Id', 'Place ID', 'place_id'
        ]);

        $mapped['kgmid'] = $this->getFieldValue($row, [
            'Kgmid', 'kgmid', 'U: Kgmid', 'KGMID', 'kgmid'
        ]);

        $mapped['plus_code'] = $this->getFieldValue($row, [
            'Plus code', 'plus_code', 'V: Plus code', 'Plus Code', 'plus_code'
        ]);

        $mapped['google_knowledge_url'] = $this->getFieldValue($row, [
            'Google Knowled', 'google_knowledge_url', 'W: Google Knowled', 'Google Knowledge URL', 'google_knowledge_url'
        ]);

        return $mapped;
    }

    /**
     * Get field value from row with multiple possible keys
     */
    protected function getFieldValue($row, $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                $value = trim($row[$key]);
                // Skip "Waiting for pi" or similar placeholder values
                if (stripos($value, 'waiting for') !== false) {
                    continue;
                }
                return $value;
            }
        }
        return $default;
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
                'phone' => $data['phone'] ?? null,
                'phones' => $data['phones'] ?? null,
                'company' => $data['company'] ?? null,
                'category' => $this->category,
                'address' => $data['address'] ?? null,
                'full_address' => $data['full_address'] ?? $data['address'] ?? null,
                'street' => $data['street'] ?? null,
                'municipality' => $data['municipality'] ?? null,
                'website' => $data['website'] ?? null,
                'domain' => $data['domain'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'twitter' => $data['twitter'] ?? null,
                'latitude' => $this->parseDecimal($data['latitude'] ?? null),
                'longitude' => $this->parseDecimal($data['longitude'] ?? null),
                'rating' => $this->parseDecimal($data['rating'] ?? null),
                'average_rating' => $this->parseDecimal($data['average_rating'] ?? null),
                'review_count' => $this->parseInteger($data['review_count'] ?? 0),
                'review_url' => $data['review_url'] ?? null,
                'google_maps_url' => $data['google_maps_url'] ?? null,
                'opening_hours' => $data['opening_hours'] ?? null,
                'featured_image' => $data['featured_image'] ?? null,
                'cid' => $data['cid'] ?? null,
                'place_id' => $data['place_id'] ?? null,
                'kgmid' => $data['kgmid'] ?? null,
                'plus_code' => $data['plus_code'] ?? null,
                'google_knowledge_url' => $data['google_knowledge_url'] ?? null,
                'claimed' => $this->parseBoolean($data['claimed'] ?? false),
                'notes' => $data['notes'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Remove null values to use database defaults
            $leadData = array_filter($leadData, function($value) {
                return $value !== null && $value !== '';
            });

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
