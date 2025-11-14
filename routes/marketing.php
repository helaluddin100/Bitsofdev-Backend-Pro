<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ResponseController;

/*
|--------------------------------------------------------------------------
| Marketing Campaign Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the marketing campaign management system.
| All routes are protected by admin middleware.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin/marketing')->name('admin.marketing.')->group(function () {

    // Marketing Dashboard
    Route::get('/', [MarketingController::class, 'index'])->name('dashboard');
    Route::get('/analytics/leads', [MarketingController::class, 'leadsAnalytics'])->name('analytics.leads');
    Route::get('/analytics/campaigns', [MarketingController::class, 'campaignAnalytics'])->name('analytics.campaigns');
    Route::get('/export/leads', [MarketingController::class, 'exportLeads'])->name('export.leads');

    // Lead Management
    Route::resource('leads', LeadController::class);
    Route::post('leads/bulk-delete', [LeadController::class, 'bulkDelete'])->name('leads.bulk-delete');
    Route::post('leads/{lead}/toggle-status', [LeadController::class, 'toggleStatus'])->name('leads.toggle-status');
    Route::get('leads/import/form', [LeadController::class, 'importForm'])->name('leads.import.form');
    Route::post('leads/import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('leads/template/download', [LeadController::class, 'downloadTemplate'])->name('leads.template.download');
    Route::get('leads/statistics', [LeadController::class, 'statistics'])->name('leads.statistics');
    Route::get('leads/test-import', [LeadController::class, 'testImport'])->name('leads.test-import');

    // Campaign Management
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/add-leads', [CampaignController::class, 'addLeads'])->name('campaigns.add-leads');
    Route::post('campaigns/{campaign}/remove-leads', [CampaignController::class, 'removeLeads'])->name('campaigns.remove-leads');
    Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
    Route::post('campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
    Route::get('campaigns/{campaign}/performance', [CampaignController::class, 'performance'])->name('campaigns.performance');
    Route::get('campaigns/{campaign}/export', [CampaignController::class, 'export'])->name('campaigns.export');
    Route::post('campaigns/{campaign}/toggle-reminders', [CampaignController::class, 'toggleReminders'])->name('campaigns.toggle-reminders');
    Route::post('campaigns/{campaign}/leads/{lead}/remove-from-reminders', [CampaignController::class, 'removeFromReminders'])->name('campaigns.remove-from-reminders');

    // Response Management
    Route::resource('responses', ResponseController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('responses/{response}/mark-qualified', [ResponseController::class, 'markQualified'])->name('responses.mark-qualified');
    Route::post('responses/{response}/mark-unqualified', [ResponseController::class, 'markUnqualified'])->name('responses.mark-unqualified');
    Route::post('responses/bulk-update', [ResponseController::class, 'bulkUpdate'])->name('responses.bulk-update');
    Route::get('responses/statistics', [ResponseController::class, 'statistics'])->name('responses.statistics');
    Route::get('responses/export', [ResponseController::class, 'export'])->name('responses.export');

    // Jobs Monitoring
    Route::get('jobs', [\App\Http\Controllers\Admin\JobMonitorController::class, 'index'])->name('jobs.index');
    Route::get('jobs/data', [\App\Http\Controllers\Admin\JobMonitorController::class, 'getJobs'])->name('jobs.data');
    Route::post('jobs/{id}/retry', [\App\Http\Controllers\Admin\JobMonitorController::class, 'retry'])->name('jobs.retry');
    Route::delete('jobs/failed/{id}', [\App\Http\Controllers\Admin\JobMonitorController::class, 'deleteFailed'])->name('jobs.delete-failed');
    // Failed Emails
    Route::post('jobs/failed-email/{id}/resend', [\App\Http\Controllers\Admin\JobMonitorController::class, 'resendFailedEmail'])->name('jobs.resend-failed-email');
    Route::delete('jobs/failed-email/{id}', [\App\Http\Controllers\Admin\JobMonitorController::class, 'deleteFailedEmail'])->name('jobs.delete-failed-email');
});

// Public routes for email tracking and unsubscribing
Route::get('/unsubscribe', function (\Illuminate\Http\Request $request) {
    $campaignId = $request->get('campaign');
    $leadId = $request->get('lead');
    $token = $request->get('token');

    // Verify token
    $expectedToken = hash('sha256', $campaignId . $leadId . config('app.key'));

    if ($token !== $expectedToken) {
        return response()->json(['error' => 'Invalid unsubscribe link'], 400);
    }

    // Find campaign lead and mark as unsubscribed
    $campaignLead = \App\Models\CampaignLead::where('campaign_id', $campaignId)
        ->where('lead_id', $leadId)
        ->first();

    if ($campaignLead) {
        $campaignLead->markAsUnsubscribed();
        return view('emails.unsubscribed');
    }

    return response()->json(['error' => 'Campaign lead not found'], 404);
})->name('unsubscribe');

Route::get('/track/email', function (\Illuminate\Http\Request $request) {
    $campaignId = $request->get('campaign');
    $leadId = $request->get('lead');
    $token = $request->get('token');

    // Verify token
    $expectedToken = hash('sha256', $campaignId . $leadId . 'track' . config('app.key'));

    if ($token !== $expectedToken) {
        return response()->json(['error' => 'Invalid tracking link'], 400);
    }

    // Log email open (you can store this in a separate table if needed)
    \Illuminate\Support\Facades\Log::info("Email opened: Campaign {$campaignId}, Lead {$leadId}");

    // Return 1x1 transparent pixel
    $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

    return response($pixel, 200, [
        'Content-Type' => 'image/gif',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('track.email');
