<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AISettings;
use App\Models\QAPair;
use Illuminate\Support\Facades\Log;

class AISettingsController extends Controller
{
    /**
     * Get current AI settings
     */
    public function getSettings()
    {
        try {
            $settings = AISettings::getCurrent();
            $learningStats = $this->getLearningStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'settings' => $settings,
                    'learning_stats' => $learningStats
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting AI settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI settings'
            ], 500);
        }
    }

    /**
     * Update AI settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'ai_provider' => 'required|in:gemini,own_ai,none',
                'training_mode' => 'boolean',
                'learning_threshold' => 'integer|min:1|max:100',
                'use_static_responses' => 'boolean'
            ]);

            $settings = AISettings::updateSettings($validated);

            Log::info('AI settings updated: ' . json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'AI settings updated successfully',
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating AI settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update AI settings'
            ], 500);
        }
    }

    /**
     * Switch AI provider
     */
    public function switchProvider(Request $request)
    {
        try {
            $provider = $request->input('provider', 'gemini');

            if (!in_array($provider, ['gemini', 'own_ai', 'none'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid AI provider'
                ], 400);
            }

            $settings = AISettings::updateSettings(['ai_provider' => $provider]);

            Log::info('AI provider switched to: ' . $provider);

            return response()->json([
                'success' => true,
                'message' => "AI provider switched to {$provider}",
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Error switching AI provider: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch AI provider'
            ], 500);
        }
    }

    /**
     * Toggle training mode
     */
    public function toggleTrainingMode(Request $request)
    {
        try {
            $trainingMode = $request->input('training_mode', false);
            $settings = AISettings::updateSettings(['training_mode' => $trainingMode]);

            $mode = $trainingMode ? 'enabled' : 'disabled';
            Log::info('AI training mode ' . $mode);

            return response()->json([
                'success' => true,
                'message' => "AI training mode {$mode}",
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling training mode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle training mode'
            ], 500);
        }
    }

    /**
     * Toggle static responses
     */
    public function toggleStaticResponses(Request $request)
    {
        try {
            $useStatic = $request->input('use_static_responses', false);
            $settings = AISettings::updateSettings(['use_static_responses' => $useStatic]);

            $status = $useStatic ? 'enabled' : 'disabled';
            Log::info('Static responses ' . $status);

            return response()->json([
                'success' => true,
                'message' => "Static responses {$status}",
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling static responses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle static responses'
            ], 500);
        }
    }

    /**
     * Get learning statistics
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
}
