<?php

namespace App\Http\Controllers;

use App\Models\QAPair;
use App\Models\VisitorQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QAPairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qaPairs = QAPair::where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $qaPairs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $qaPair = QAPair::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Q&A pair created successfully',
            'data' => $qaPair
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $qaPair = QAPair::find($id);

        if (!$qaPair) {
            return response()->json([
                'success' => false,
                'message' => 'Q&A pair not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $qaPair
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $qaPair = QAPair::find($id);

        if (!$qaPair) {
            return response()->json([
                'success' => false,
                'message' => 'Q&A pair not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'sometimes|required|string|max:255',
            'answer_1' => 'sometimes|required|string',
            'answer_2' => 'nullable|string',
            'answer_3' => 'nullable|string',
            'answer_4' => 'nullable|string',
            'answer_5' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $qaPair->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Q&A pair updated successfully',
            'data' => $qaPair
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $qaPair = QAPair::find($id);

        if (!$qaPair) {
            return response()->json([
                'success' => false,
                'message' => 'Q&A pair not found'
            ], 404);
        }

        $qaPair->delete();

        return response()->json([
            'success' => true,
            'message' => 'Q&A pair deleted successfully'
        ]);
    }

    /**
     * Get AI response based on user question
     */
        public function getAIResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Question is required',
                'errors' => $validator->errors()
            ], 422);
        }

        $userQuestion = strtolower(trim($request->question));

        // Store visitor question
        $visitorQuestion = VisitorQuestion::create([
            'question' => $request->question,
            'visitor_ip' => $request->ip(),
            'visitor_session' => $request->session()->getId(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending'
        ]);

        // Find matching Q&A pair
        $qaPair = QAPair::where('is_active', true)
            ->get()
            ->first(function ($qa) use ($userQuestion) {
                $question = strtolower(trim($qa->question));
                return str_contains($userQuestion, $question) || str_contains($question, $userQuestion);
            });

        if (!$qaPair) {
            // Update visitor question with no match status
            $visitorQuestion->update([
                'status' => 'no_match',
                'admin_notes' => 'No matching Q&A pair found'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No matching answer found for your question',
                'suggestion' => 'Please try rephrasing your question or contact our support team.',
                'visitor_question_id' => $visitorQuestion->id
            ], 404);
        }

        // Update visitor question with matched Q&A
        $visitorQuestion->update([
            'qa_pair_id' => $qaPair->id,
            'status' => 'answered'
        ]);

        // Increment usage count
        $qaPair->incrementUsage();

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
                'usage_count' => $qaPair->usage_count + 1,
                'visitor_question_id' => $visitorQuestion->id
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
}
