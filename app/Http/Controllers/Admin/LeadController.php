<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Imports\LeadsImport;
use App\Imports\ExcelReader;
use App\Services\CsvProcessor;
use App\Jobs\ProcessLeadImportJob;

class LeadController extends Controller
{
    /**
     * Display a listing of leads
     */
    public function index(Request $request)
    {
        $query = Lead::with('category');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('municipality')) {
            $query->where('municipality', $request->municipality);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('claimed')) {
            $query->where('claimed', $request->claimed === 'true');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get all leads without pagination (DataTable will handle pagination client-side)
        $leads = $query->get();
        $categories = Category::active()->get();
        $municipalities = Lead::select('municipality')
            ->whereNotNull('municipality')
            ->distinct()
            ->pluck('municipality')
            ->sort();

        return view('admin.marketing.leads.index', compact(
            'leads',
            'categories',
            'municipalities'
        ));
    }

    /**
     * Show the form for creating a new lead
     */
    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.marketing.leads.create', compact('categories'));
    }

    /**
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'address' => 'nullable|string',
            'municipality' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'yelp' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicates
        if (Lead::isDuplicate($request->email, $request->phone)) {
            return redirect()->back()
                ->with('error', 'A lead with this email or phone already exists.')
                ->withInput();
        }

        $lead = Lead::create($request->all());

        return redirect()->route('admin.marketing.leads.show', $lead)
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead
     */
    public function show(Lead $lead)
    {
        $lead->load(['campaigns', 'responses.campaign']);

        // Get campaign statistics for this lead
        $campaignStats = [
            'total_campaigns' => $lead->campaigns->count(),
            'responded_campaigns' => $lead->campaigns->where('pivot.status', 'responded')->count(),
            'response_rate' => $lead->response_rate,
            'last_contacted' => $lead->last_contacted_at,
            'contact_count' => $lead->contact_count
        ];

        return view('admin.marketing.leads.show', compact('lead', 'campaignStats'));
    }

    /**
     * Show the form for editing the lead
     */
    public function edit(Lead $lead)
    {
        $categories = Category::active()->get();
        return view('admin.marketing.leads.edit', compact('lead', 'categories'));
    }

    /**
     * Update the specified lead
     */
    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'address' => 'nullable|string',
            'municipality' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'yelp' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicates (excluding current lead)
        if ($request->email || $request->phone) {
            $duplicate = Lead::where('id', '!=', $lead->id)
                ->where(function($query) use ($request) {
                    if ($request->email) {
                        $query->where('email', $request->email);
                    }
                    if ($request->phone) {
                        $query->orWhere('phone', $request->phone);
                    }
                })
                ->exists();

            if ($duplicate) {
                return redirect()->back()
                    ->with('error', 'A lead with this email or phone already exists.')
                    ->withInput();
            }
        }

        $lead->update($request->all());

        return redirect()->route('admin.marketing.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.marketing.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Bulk delete leads
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid lead selection.');
        }

        $deletedCount = Lead::whereIn('id', $request->lead_ids)->delete();

        return redirect()->back()
            ->with('success', "{$deletedCount} leads deleted successfully.");
    }

    /**
     * Toggle lead active status
     */
    public function toggleStatus(Lead $lead)
    {
        $lead->update(['is_active' => !$lead->is_active]);

        $status = $lead->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Lead {$status} successfully.");
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        $categories = Category::active()->get();
        return view('admin.marketing.leads.import', compact('categories'));
    }

    /**
     * Import leads from Excel/CSV
     */
    public function import(Request $request)
    {
        // Debug request data
        Log::info("Import request received");
        Log::info("Request data: " . json_encode($request->all()));
        Log::info("Has file: " . ($request->hasFile('file') ? 'Yes' : 'No'));

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::info("File details: " . json_encode([
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension()
            ]));
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'category' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            Log::error("Validation failed: " . json_encode($validator->errors()->all()));
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Log::info("Starting lead import for category: " . $request->category);

            // Store file temporarily
            $file = $request->file('file');
            $fileName = 'leads_import_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName);
            $fullPath = storage_path('app/' . $filePath);

            // Check file size and row count for queue decision
            $fileSize = $file->getSize();
            $rowCount = $this->countCsvRows($fullPath);

            Log::info("File info: Size={$fileSize} bytes, Rows={$rowCount}");

            // For large files (>500 rows or >1MB), use queue
            if ($rowCount > 500 || $fileSize > 1024 * 1024) {
                Log::info("Large file detected, using queue processing");

                // Dispatch job to queue
                ProcessLeadImportJob::dispatch($fullPath, $request->category, auth()->id());

                return redirect()->route('admin.marketing.leads.index')
                    ->with('success', 'Large file detected. Import has been queued and will be processed in the background. You will be notified when complete.');
            }

            // For small files, process immediately
            Log::info("Small file detected, processing immediately");
            $processor = new CsvProcessor($request->category);
            $result = $processor->processFile($fullPath);

            $importedCount = $result['imported'];
            $skippedCount = $result['skipped'];
            $errors = $result['errors'];

            $message = "Import completed. {$importedCount} leads imported successfully.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} leads skipped.";
            }

            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
                Log::warning('Lead import errors', $errors);
            }

            // Clean up temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return redirect()->route('admin.marketing.leads.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Lead import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Count rows in CSV file
     */
    protected function countCsvRows($filePath)
    {
        $count = 0;
        $handle = fopen($filePath, 'r');

        if ($handle) {
            // Skip header row
            fgetcsv($handle);

            while (fgetcsv($handle) !== false) {
                $count++;
            }
            fclose($handle);
        }

        return $count;
    }

    /**
     * Test import with sample data
     */
    public function testImport()
    {
        try {
            Log::info("Testing import with sample data");

            $processor = new CsvProcessor('Test Category');
            $result = $processor->processFile(public_path('test_leads.csv'));

            $importedCount = $result['imported'];
            $skippedCount = $result['skipped'];
            $errors = $result['errors'];

            $message = "Test import completed. {$importedCount} leads imported successfully.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} leads skipped.";
            }

            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
                Log::warning('Test import errors', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Test import failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Test import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $filename = 'leads_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'name', 'email', 'phone', 'phones', 'company', 'full_address',
                'street', 'municipality', 'website', 'domain', 'facebook',
                'instagram', 'twitter', 'yelp', 'latitude', 'longitude',
                'rating', 'review_count', 'claimed', 'notes'
            ]);

            // Sample data row
            fputcsv($file, [
                'Sample Business', 'sample@example.com', '+1234567890', '["+1234567890","+0987654321"]',
                'Sample Company', '123 Main St, City, State', 'Main Street', 'Sample City',
                'https://example.com', 'example.com', 'https://facebook.com/sample',
                'https://instagram.com/sample', 'https://twitter.com/sample', 'https://yelp.com/sample',
                '40.7128', '-74.0060', '4.5', '25', 'true', 'Sample notes'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get lead statistics
     */
    public function statistics()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'active_leads' => Lead::active()->count(),
            'leads_by_category' => Lead::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get(),
            'leads_by_municipality' => Lead::select('municipality', DB::raw('count(*) as count'))
                ->whereNotNull('municipality')
                ->groupBy('municipality')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_leads' => Lead::with('category')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json($stats);
    }
}
