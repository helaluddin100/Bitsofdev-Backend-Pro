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
        
        // Check for quick answers first
        $quickAnswer = $this->getQuickAnswer($userQuestion);
        if ($quickAnswer) {
            $visitorQuestion->update([
                'answer' => $quickAnswer,
                'status' => 'answered',
                'admin_notes' => 'Quick answer provided'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'question' => $userQuestion,
                    'response' => $quickAnswer,
                    'type' => 'quick_answer',
                    'visitor_question_id' => $visitorQuestion->id
                ]
            ]);
        }
        
        // Find matching Q&A pair
        $qaPair = QAPair::where('is_active', true)
            ->get()
            ->first(function ($qa) use ($userQuestion) {
                $question = strtolower(trim($qa->question));
                return str_contains($userQuestion, $question) || str_contains($question, $userQuestion);
            });

        if (!$qaPair) {
            // Try to generate answer from website data
            $websiteAnswer = $this->generateAnswerFromWebsiteData($userQuestion);
            if ($websiteAnswer) {
                $visitorQuestion->update([
                    'answer' => $websiteAnswer,
                    'status' => 'answered',
                    'admin_notes' => 'Generated from website data'
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'question' => $userQuestion,
                        'response' => $websiteAnswer,
                        'type' => 'website_data',
                        'visitor_question_id' => $visitorQuestion->id
                    ]
                ]);
            }

            // Update visitor question with no match status
            $visitorQuestion->update([
                'status' => 'no_match',
                'admin_notes' => 'No matching Q&A pair found'
            ]);

            // Generate contact suggestion with link
            $contactSuggestion = $this->generateContactSuggestion($request->question);

            return response()->json([
                'success' => false,
                'message' => 'No matching answer found for your question',
                'suggestion' => $contactSuggestion,
                'contact_link' => config('app.url') . '/contact',
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
                'type' => 'qa_pair',
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

    /**
     * Get quick answer for common questions
     */
    private function getQuickAnswer($question)
    {
        $quickAnswers = [
            // Date and Time
            'date' => 'Today is ' . now()->format('l, F j, Y'),
            'time' => 'Current time is ' . now()->format('g:i A'),
            'today' => 'Today is ' . now()->format('l, F j, Y'),
            'aj koto tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'aj ki tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'current date' => 'Today is ' . now()->format('l, F j, Y'),
            'current time' => 'Current time is ' . now()->format('g:i A'),
            
            // Greetings
            'hello' => 'Hello! How can I help you today?',
            'hi' => 'Hi there! What can I do for you?',
            'good morning' => 'Good morning! How can I assist you?',
            'good afternoon' => 'Good afternoon! What can I help you with?',
            'good evening' => 'Good evening! How may I help you?',
            'assalamu alaikum' => 'Wa Alaikum Assalam! How can I help you?',
            'salam' => 'Wa Alaikum Assalam! What can I do for you?',
            
            // Company Info
            'company name' => 'We are BitsOfDev - a leading web development company.',
            'who are you' => 'I am the AI assistant for BitsOfDev. I can help you with information about our services, projects, and more.',
            'what is bitsofdev' => 'BitsOfDev is a professional web development company specializing in modern web technologies and digital solutions.',
            
            // Contact
            'contact' => 'You can contact us through our contact form on the website or reach out to us directly.',
            'phone' => 'For phone inquiries, please check our contact page for the latest contact information.',
            'email' => 'You can reach us via email through our contact form on the website.',
            
            // Website Info
            'website' => 'You are currently on the BitsOfDev website. We offer web development, mobile app development, and digital solutions.',
            'services' => 'We offer web development, mobile app development, UI/UX design, and digital consultation services.',
        ];

        foreach ($quickAnswers as $keyword => $answer) {
            if (str_contains($question, $keyword)) {
                return $answer;
            }
        }

        return null;
    }

    /**
     * Generate answer from website data
     */
    private function generateAnswerFromWebsiteData($question)
    {
        // Get website data
        $websiteData = $this->getWebsiteData();
        
        // Check for blog-related questions
        if (str_contains($question, 'blog') || str_contains($question, 'article') || str_contains($question, 'post')) {
            return $this->generateBlogAnswer($websiteData['blogs'], $question);
        }
        
        // Check for project-related questions
        if (str_contains($question, 'project') || str_contains($question, 'portfolio') || str_contains($question, 'work')) {
            return $this->generateProjectAnswer($websiteData['projects'], $question);
        }
        
        // Check for team-related questions
        if (str_contains($question, 'team') || str_contains($question, 'member') || str_contains($question, 'developer')) {
            return $this->generateTeamAnswer($websiteData['team'], $question);
        }
        
        // Check for about-related questions
        if (str_contains($question, 'about') || str_contains($question, 'company') || str_contains($question, 'story')) {
            return $this->generateAboutAnswer($websiteData['about'], $question);
        }
        
        // Check for pricing-related questions
        if (str_contains($question, 'price') || str_contains($question, 'cost') || str_contains($question, 'package')) {
            return $this->generatePricingAnswer($websiteData['pricing'], $question);
        }

        return null;
    }

    /**
     * Get website data from API
     */
    private function getWebsiteData()
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
            
            return $data;
        } catch (\Exception $e) {
            return [
                'blogs' => [],
                'projects' => [],
                'team' => [],
                'about' => [],
                'pricing' => [],
            ];
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
     * Generate blog-related answer
     */
    private function generateBlogAnswer($blogs, $question)
    {
        if (empty($blogs)) {
            return "We have a blog section on our website where we share insights about web development, technology trends, and industry updates. You can check it out to read our latest articles.";
        }

        $totalBlogs = count($blogs);
        $featuredBlogs = array_slice($blogs, 0, 3);
        
        $answer = "We have {$totalBlogs} blog posts on our website covering various topics including web development, technology trends, and industry insights. ";
        
        if (!empty($featuredBlogs)) {
            $answer .= "Some of our recent posts include: ";
            foreach ($featuredBlogs as $blog) {
                $answer .= "\"".$blog['title']."\", ";
            }
            $answer = rtrim($answer, ', ') . ". ";
        }
        
        $answer .= "You can visit our blog section to read the full articles and stay updated with the latest trends in web development.";
        
        return $answer;
    }

    /**
     * Generate project-related answer
     */
    private function generateProjectAnswer($projects, $question)
    {
        if (empty($projects)) {
            return "We have completed numerous projects for various clients. You can check our portfolio section to see our work and the technologies we use.";
        }

        $totalProjects = count($projects);
        $featuredProjects = array_slice($projects, 0, 3);
        
        $answer = "We have completed {$totalProjects} projects for various clients across different industries. ";
        
        if (!empty($featuredProjects)) {
            $answer .= "Some of our notable projects include: ";
            foreach ($featuredProjects as $project) {
                $answer .= "\"".$project['title']."\", ";
            }
            $answer = rtrim($answer, ', ') . ". ";
        }
        
        $answer .= "You can visit our projects section to see our portfolio and learn more about our development capabilities.";
        
        return $answer;
    }

    /**
     * Generate team-related answer
     */
    private function generateTeamAnswer($team, $question)
    {
        if (empty($team)) {
            return "We have a talented team of developers, designers, and digital experts. You can learn more about our team members in the team section of our website.";
        }

        $totalMembers = count($team);
        $featuredMembers = array_slice($team, 0, 3);
        
        $answer = "We have {$totalMembers} team members including developers, designers, and digital experts. ";
        
        if (!empty($featuredMembers)) {
            $answer .= "Some of our key team members include: ";
            foreach ($featuredMembers as $member) {
                $answer .= $member['name'] . " (".$member['position']."), ";
            }
            $answer = rtrim($answer, ', ') . ". ";
        }
        
        $answer .= "You can visit our team section to learn more about our talented professionals.";
        
        return $answer;
    }

    /**
     * Generate about-related answer
     */
    private function generateAboutAnswer($about, $question)
    {
        if (empty($about)) {
            return "BitsOfDev is a professional web development company. You can learn more about our story, values, and mission in the about section of our website.";
        }

        $answer = "BitsOfDev is a professional web development company. ";
        
        if (isset($about['description'])) {
            $answer .= $about['description'] . " ";
        }
        
        $answer .= "You can visit our about section to learn more about our company story, values, and mission.";
        
        return $answer;
    }

    /**
     * Generate pricing-related answer
     */
    private function generatePricingAnswer($pricing, $question)
    {
        if (empty($pricing)) {
            return "We offer various pricing packages for our services. You can check our pricing section to see our different plans and what's included in each package.";
        }

        $totalPlans = count($pricing);
        $answer = "We offer {$totalPlans} different pricing plans to suit various needs and budgets. ";
        
        $answer .= "You can visit our pricing section to see detailed information about our packages, what's included, and choose the plan that best fits your requirements.";
        
        return $answer;
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
