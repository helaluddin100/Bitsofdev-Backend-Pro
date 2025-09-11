<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessRemindersJob;

class ProcessRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing:process-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process marketing campaign reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing marketing reminders...');

        // Dispatch the job to process reminders
        ProcessRemindersJob::dispatch();

        $this->info('Reminder processing job dispatched successfully.');

        return Command::SUCCESS;
    }
}
