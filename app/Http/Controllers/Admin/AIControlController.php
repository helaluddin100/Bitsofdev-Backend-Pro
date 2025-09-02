<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AISettings;
use App\Models\QAPair;
use Illuminate\Support\Facades\Log;

class AIControlController extends Controller
{
    /**
     * Show AI Control Dashboard
     */
    public function index()
    {
        $settings = AISettings::getCurrent();
        $learningStats = $this->getLearningStats();
        $recentQuestions = $this->getRecentQuestions();

        return view('admin.ai-control', compact('settings', 'learningStats', 'recentQuestions'));
    }

    /**
     * Update AI Settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:gemini,own_ai,none',
            'training_mode' => 'boolean',
            'learning_threshold' => 'integer|min:1|max:100',
            'use_static_responses' => 'boolean'
        ]);

        try {
            $settings = AISettings::updateSettings($request->all());

            Log::info('AI settings updated from admin dashboard: ' . json_encode($request->all()));

            return redirect()->back()->with('success', 'AI settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating AI settings from admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update AI settings.');
        }
    }

    /**
     * Switch AI Provider
     */
    public function switchProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:gemini,own_ai,none'
        ]);

        try {
            $settings = AISettings::updateSettings(['ai_provider' => $request->provider]);

            Log::info('AI provider switched from admin dashboard to: ' . $request->provider);

            return redirect()->back()->with('success', "AI provider switched to {$request->provider}!");
        } catch (\Exception $e) {
            Log::error('Error switching AI provider from admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to switch AI provider.');
        }
    }

    /**
     * Toggle Training Mode
     */
    public function toggleTrainingMode(Request $request)
    {
        $trainingMode = $request->input('training_mode', false);

        try {
            $settings = AISettings::updateSettings(['training_mode' => $trainingMode]);

            $mode = $trainingMode ? 'enabled' : 'disabled';
            Log::info('AI training mode ' . $mode . ' from admin dashboard');

            return redirect()->back()->with('success', "AI training mode {$mode}!");
        } catch (\Exception $e) {
            Log::error('Error toggling training mode from admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to toggle training mode.');
        }
    }

    /**
     * Toggle Static Responses
     */
    public function toggleStaticResponses(Request $request)
    {
        $useStatic = $request->input('use_static_responses', false);

        try {
            $settings = AISettings::updateSettings(['use_static_responses' => $useStatic]);

            $status = $useStatic ? 'enabled' : 'disabled';
            Log::info('Static responses ' . $status . ' from admin dashboard');

            return redirect()->back()->with('success', "Static responses {$status}!");
        } catch (\Exception $e) {
            Log::error('Error toggling static responses from admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to toggle static responses.');
        }
    }

    /**
     * Activate Learned Responses
     */
    public function activateLearned()
    {
        try {
            $activated = QAPair::where('category', 'ai_learned')
                ->where('is_active', false)
                ->update(['is_active' => true]);

            Log::info("Activated {$activated} learned responses from admin dashboard");

            return redirect()->back()->with('success', "Activated {$activated} learned responses!");
        } catch (\Exception $e) {
            Log::error('Error activating learned responses from admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to activate learned responses.');
        }
    }

    /**
     * Get Learning Statistics
     */
    private function getLearningStats()
    {
        $totalLearned = QAPair::where('category', 'ai_learned')->count();
        $activeLearned = QAPair::where('category', 'ai_learned')->where('is_active', true)->count();
        $pendingReview = QAPair::where('category', 'ai_learned')->where('is_active', false)->count();

        $settings = AISettings::getCurrent();
        $learningProgress = $settings->learning_threshold > 0 ?
            min(100, ($activeLearned / $settings->learning_threshold) * 100) : 0;

        return [
            'total_learned' => $totalLearned,
            'active_learned' => $activeLearned,
            'pending_review' => $pendingReview,
            'learning_progress' => round($learningProgress, 2),
            'can_activate_own_ai' => $activeLearned >= $settings->learning_threshold
        ];
    }

    /**
     * Get Recent Questions
     */
    private function getRecentQuestions()
    {
        return QAPair::where('category', 'ai_learned')
            ->latest()
            ->take(10)
            ->get(['id', 'question', 'answer_1', 'is_active', 'created_at']);
    }
}
