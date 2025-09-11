<?php

namespace App\Jobs;

use App\Services\CsvProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLeadImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $category;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $category, $userId = null)
    {
        $this->filePath = $filePath;
        $this->category = $category;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info("Starting lead import job for category: {$this->category}");

            $processor = new CsvProcessor($this->category);
            $result = $processor->processFile($this->filePath);

            Log::info("Lead import job completed", [
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
                'errors' => count($result['errors'])
            ]);

            // Clean up uploaded file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

        } catch (\Exception $e) {
            Log::error("Lead import job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Lead import job failed permanently: " . $exception->getMessage());

        // Clean up uploaded file
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
