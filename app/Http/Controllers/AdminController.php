<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QAPair;
use App\Models\VisitorQuestion;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $qaPairs = QAPair::orderBy('usage_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_qa_pairs' => QAPair::count(),
            'active_qa_pairs' => QAPair::where('is_active', true)->count(),
            'total_usage' => QAPair::sum('usage_count'),
            'most_used' => QAPair::orderBy('usage_count', 'desc')->first(),
            'total_questions' => VisitorQuestion::count(),
            'pending_questions' => VisitorQuestion::pending()->count(),
            'answered_questions' => VisitorQuestion::answered()->count(),
            'converted_questions' => VisitorQuestion::converted()->count(),
            'recent_questions' => VisitorQuestion::with('qaPair')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return view('admin.ai-dashboard', compact('qaPairs', 'stats'));
    }

    /**
     * Show Q&A management page
     */
    public function qaManagement()
    {
        $qaPairs = QAPair::orderBy('created_at', 'desc')->get();
        return view('admin.qa-management', compact('qaPairs'));
    }

    /**
     * Store new Q&A pair
     */
    public function storeQA(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer_1' => 'required|string',
            'answer_2' => 'nullable|string',
            'answer_3' => 'nullable|string',
            'answer_4' => 'nullable|string',
            'answer_5' => 'nullable|string',
            'category' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        QAPair::create($request->all());

        return redirect()->route('admin.qa-management')
            ->with('success', 'Q&A pair created successfully!');
    }

    /**
     * Update Q&A pair
     */
    public function updateQA(Request $request, $id)
    {
        $qaPair = QAPair::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer_1' => 'required|string',
            'answer_2' => 'nullable|string',
            'answer_3' => 'nullable|string',
            'answer_4' => 'nullable|string',
            'answer_5' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $qaPair->update($request->all());

        return redirect()->route('admin.qa-management')
            ->with('success', 'Q&A pair updated successfully!');
    }

    /**
     * Delete Q&A pair
     */
    public function deleteQA($id)
    {
        $qaPair = QAPair::findOrFail($id);
        $qaPair->delete();

        return redirect()->route('admin.qa-management')
            ->with('success', 'Q&A pair deleted successfully!');
    }

    /**
     * Toggle Q&A pair status
     */
    public function toggleStatus($id)
    {
        $qaPair = QAPair::findOrFail($id);
        $qaPair->update(['is_active' => !$qaPair->is_active]);

        $status = $qaPair->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.qa-management')
            ->with('success', "Q&A pair {$status} successfully!");
    }

    /**
     * Test AI response
     */
    public function testAIResponse(Request $request)
    {
        $question = $request->input('test_question');

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question is required'
            ], 422);
        }

        $userQuestion = strtolower(trim($question));

        // Find matching Q&A pair
        $qaPair = QAPair::where('is_active', true)
            ->get()
            ->first(function ($qa) use ($userQuestion) {
                $question = strtolower(trim($qa->question));
                return str_contains($userQuestion, $question) || str_contains($question, $userQuestion);
            });

        if (!$qaPair) {
            return response()->json([
                'success' => false,
                'message' => 'No matching answer found for your question',
                'suggestion' => 'Please try rephrasing your question or add this question to your Q&A database.'
            ], 404);
        }

        // Get all available answers
        $answers = $qaPair->answers;

        // Generate AI response by combining answers
        $aiResponse = $this->generateAIResponse($answers, $userQuestion);

        return response()->json([
            'success' => true,
            'data' => [
                'question' => $userQuestion,
                'response' => $aiResponse,
                'matched_qa_id' => $qaPair->id,
                'usage_count' => $qaPair->usage_count
            ]
        ]);
    }

    /**
     * Generate AI response by combining multiple answers
     */
    private function generateAIResponse($answers, $question)
    {
        if (count($answers) === 1) {
            return $answers[0];
        }

        // Simple AI response generation by combining answers
        $response = $answers[0];

        if (count($answers) > 1) {
            $response .= "\n\nAdditionally, " . lcfirst($answers[1]);
        }

        if (count($answers) > 2) {
            $response .= " Also, " . lcfirst($answers[2]);
        }

        return $response;
    }

    /**
     * Show visitor questions management page
     */
    public function visitorQuestions()
    {
        $visitorQuestions = VisitorQuestion::with('qaPair')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_questions' => VisitorQuestion::count(),
            'pending_questions' => VisitorQuestion::pending()->count(),
            'answered_questions' => VisitorQuestion::answered()->count(),
            'converted_questions' => VisitorQuestion::converted()->count(),
            'no_match_questions' => VisitorQuestion::where('status', 'no_match')->count()
        ];

        return view('admin.visitor-questions', compact('visitorQuestions', 'stats'));
    }

    /**
     * Answer a visitor question manually
     */
    public function answerVisitorQuestion(Request $request, $id)
    {
        $visitorQuestion = VisitorQuestion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'answer' => 'required|string',
            'create_qa_pair' => 'boolean',
            'question' => 'required_if:create_qa_pair,true|string',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update visitor question with answer
        $visitorQuestion->update([
            'answer' => $request->answer,
            'status' => 'answered',
            'is_answered' => true,
            'admin_notes' => 'Manually answered by admin'
        ]);

        // If admin wants to create Q&A pair from this question
        if ($request->create_qa_pair) {
            QAPair::create([
                'question' => $request->question,
                'answer_1' => $request->answer,
                'category' => $request->category,
                'is_active' => true
            ]);

            $visitorQuestion->update([
                'admin_notes' => 'Manually answered and created Q&A pair'
            ]);
        }

        return redirect()->route('admin.visitor-questions')
            ->with('success', 'Question answered successfully!');
    }

    /**
     * Mark visitor question as converted
     */
    public function markAsConverted($id)
    {
        $visitorQuestion = VisitorQuestion::findOrFail($id);
        $visitorQuestion->update([
            'status' => 'converted',
            'is_converted' => true,
            'admin_notes' => 'Marked as converted by admin'
        ]);

        return redirect()->route('admin.visitor-questions')
            ->with('success', 'Question marked as converted!');
    }

    /**
     * Get visitor questions statistics for dashboard
     */
    public function getVisitorQuestionsStats()
    {
        $stats = [
            'total_questions' => VisitorQuestion::count(),
            'pending_questions' => VisitorQuestion::pending()->count(),
            'answered_questions' => VisitorQuestion::answered()->count(),
            'converted_questions' => VisitorQuestion::converted()->count(),
            'recent_questions' => VisitorQuestion::with('qaPair')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($stats);
    }
}
