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
            // Generate contact suggestion
            $contactSuggestion = $this->generateContactSuggestion($question);

            return response()->json([
                'success' => false,
                'message' => 'No matching answer found for your question',
                'suggestion' => $contactSuggestion,
                'contact_link' => config('app.url') . '/contact'
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

    /**
     * Show quick answers management page
     */
    public function quickAnswers()
    {
        $quickAnswers = [
            'date' => 'Today is ' . now()->format('l, F j, Y'),
            'time' => 'Current time is ' . now()->format('g:i A'),
            'today' => 'Today is ' . now()->format('l, F j, Y'),
            'aj koto tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'aj ki tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'current date' => 'Today is ' . now()->format('l, F j, Y'),
            'current time' => 'Current time is ' . now()->format('g:i A'),
            'hello' => 'Hello! How can I help you today?',
            'hi' => 'Hi there! What can I do for you?',
            'good morning' => 'Good morning! How can I assist you?',
            'good afternoon' => 'Good afternoon! What can I help you with?',
            'good evening' => 'Good evening! How may I help you?',
            'assalamu alaikum' => 'Wa Alaikum Assalam! How can I help you?',
            'salam' => 'Wa Alaikum Assalam! What can I do for you?',
            'company name' => 'We are sparkedev - a leading web development company.',
            'who are you' => 'I am the AI assistant for sparkedev. I can help you with information about our services, projects, and more.',
            'what is sparkedev' => 'Sparkedev is a professional web development company specializing in modern web technologies and digital solutions.',
            'what is sparkedev' => 'Sparkedev is a professional web development company specializing in modern web technologies and digital solutions.',
            'contact' => 'You can contact us through our contact form on the website or reach out to us directly.',
            'phone' => 'For phone inquiries, please check our contact page for the latest contact information.',
            'email' => 'You can reach us via email through our contact form on the website.',
            // 'website' => 'You are currently on the sparkedev website. We offer web development, mobile app development, and digital solutions.',
            'services' => 'We offer web development, mobile app development, UI/UX design, and digital consultation services.',
        ];

        return view('admin.quick-answers', compact('quickAnswers'));
    }

    /**
     * Test website data integration
     */
    public function testWebsiteData()
    {
        try {
            $baseUrl = config('app.url');

            $data = [
                'blogs' => $this->fetchApiData($baseUrl . '/api/blogs'),
                'projects' => $this->fetchApiData($baseUrl . '/api/projects'),
                'team' => $this->fetchApiData($baseUrl . '/api/team'),
                'about' => $this->fetchApiData($baseUrl . '/api/about'),
                'pricing' => $this->fetchApiData($baseUrl . '/api/pricing'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Website data fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching website data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch data from API
     */
    private function fetchApiData($url)
    {
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Generate contact suggestion based on question type
     */
    private function generateContactSuggestion($question)
    {
        $questionLower = strtolower($question);

        // Check question type and generate appropriate suggestion
        if (str_contains($questionLower, 'price') || str_contains($questionLower, 'cost') || str_contains($questionLower, 'quote')) {
            return "I don't have specific pricing information for your request. For a detailed quote and pricing information, please contact our team directly. We'd be happy to discuss your project requirements and provide you with a customized proposal. You can reach us through our contact page or call us directly.";
        }

        if (str_contains($questionLower, 'project') || str_contains($questionLower, 'development') || str_contains($questionLower, 'website')) {
            return "I'd love to help you with your project details, but I need more specific information to provide you with the best answer. Our development team can discuss your project requirements in detail. Please contact us through our contact page, and we'll schedule a consultation to understand your needs better.";
        }

        if (str_contains($questionLower, 'support') || str_contains($questionLower, 'help') || str_contains($questionLower, 'issue')) {
            return "I'm sorry I couldn't find the specific information you're looking for. Our support team is here to help you with any questions or issues you might have. Please contact us through our contact page, and we'll get back to you as soon as possible.";
        }

        if (str_contains($questionLower, 'service') || str_contains($questionLower, 'offer')) {
            return "While I can provide general information about our services, I'd recommend speaking directly with our team for detailed service information tailored to your specific needs. Please contact us through our contact page, and we'll be happy to discuss how we can help you.";
        }

        if (str_contains($questionLower, 'time') || str_contains($questionLower, 'duration') || str_contains($questionLower, 'when')) {
            return "Project timelines vary depending on the scope and complexity of your requirements. For accurate timeline estimates, our team would need to understand your specific project details. Please contact us through our contact page, and we'll provide you with a detailed project timeline.";
        }

        if (str_contains($questionLower, 'technology') || str_contains($questionLower, 'tech') || str_contains($questionLower, 'framework')) {
            return "We work with various modern technologies and frameworks. To recommend the best technology stack for your project, we'd need to understand your specific requirements and goals. Please contact us through our contact page, and our technical team will provide you with expert recommendations.";
        }

        // Default contact suggestion
        return "I apologize, but I couldn't find a specific answer to your question in our knowledge base. Our team of experts would be happy to help you with this. Please contact us through our contact page, and we'll get back to you with a detailed response. You can also call us directly for immediate assistance.";
    }
}
