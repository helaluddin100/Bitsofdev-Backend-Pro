<?php

namespace App\Http\Controllers;

use App\Models\QAPair;
use App\Models\VisitorQuestion;
use App\Models\ConversationSession;
use App\Models\ConversationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\AISettings;

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
        try {
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

            // Get AI settings
            $aiSettings = AISettings::getCurrent();

            // Get or create conversation session
            $sessionId = $request->input('sessionId') ?: ($request->hasSession() ? $request->session()->getId() : 'anonymous_' . $request->ip());
            $conversationSession = ConversationSession::getOrCreateSession(
                $sessionId,
                $request->ip(),
                $request->userAgent()
            );

            // Store visitor message
            ConversationMessage::addMessage($sessionId, 'visitor', $request->question);

            // Get full conversation context for better understanding
            $conversationHistory = $this->getConversationHistoryForContext($sessionId, 10);
            $fullContext = $this->buildConversationContext($conversationHistory, $request->question);

            // Store visitor question with error handling
            try {
                $visitorQuestion = VisitorQuestion::create([
                    'question' => $request->question,
                    'visitor_ip' => $request->ip(),
                    'visitor_session' => $request->hasSession() ? $request->session()->getId() : null,
                    'user_agent' => $request->userAgent(),
                    'status' => 'pending'
                ]);
            } catch (\Exception $e) {
                // If visitor question creation fails, continue without it
                Log::warning('Failed to create visitor question: ' . $e->getMessage());
                $visitorQuestion = null;
            }

            // Check for priority customer need patterns first (override quick answers)
            $userQuestionLower = strtolower($userQuestion);
            $isCustomerNeed = (strpos($userQuestionLower, 'i need') !== false || strpos($userQuestionLower, 'i want') !== false) && strpos($userQuestionLower, 'website') !== false;

            // Check for quick answers only if not a customer need AND static responses are enabled
            if (!$isCustomerNeed && $aiSettings->use_static_responses) {
                $quickAnswer = $this->getQuickAnswer($userQuestion, $fullContext);
                if ($quickAnswer) {
                    if ($visitorQuestion) {
                        $visitorQuestion->update([
                            'answer' => $quickAnswer,
                            'status' => 'answered',
                            'admin_notes' => 'Quick answer provided with context'
                        ]);
                    }

                    // Store quick answer in conversation history
                    ConversationMessage::addMessage($sessionId, 'ai', $quickAnswer);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'question' => $userQuestion,
                            'response' => $quickAnswer,
                            'type' => 'quick_answer',
                            'visitor_question_id' => $visitorQuestion ? $visitorQuestion->id : null
                        ]
                    ]);
                }
            }



            // Find matching Q&A pair with timeout protection (skip if customer need detected)
            $qaPair = null;
            if (!$isCustomerNeed && $aiSettings->use_static_responses) {
                try {
                    $qaPair = QAPair::where('is_active', true)
                        ->get()
                        ->first(function ($qa) use ($userQuestion) {
                            $question = strtolower(trim($qa->question));
                            $userQuestion = strtolower(trim($userQuestion));

                            // Use more strict matching - require significant overlap
                            $questionWords = explode(' ', $question);
                            $userWords = explode(' ', $userQuestion);

                            // If question is very short (1-2 words), require exact match
                            if (count($questionWords) <= 2) {
                                return $question === $userQuestion;
                            }

                            // For longer questions, require at least 70% word overlap
                            $commonWords = array_intersect($questionWords, $userWords);
                            $overlapPercentage = count($commonWords) / count($questionWords);

                            return $overlapPercentage >= 0.7;
                        });
                } catch (\Exception $e) {
                    Log::warning('Error searching Q&A pairs: ' . $e->getMessage());
                }
            }

            if (!$qaPair) {
                // Try to generate intelligent answer from website content (only if static responses are enabled)
                if ($aiSettings->use_static_responses) {
                    $intelligentAnswer = $this->generateAnswerFromWebsiteData($userQuestion);

                    if ($intelligentAnswer) {
                        // Update visitor question with intelligent answer
                        if ($visitorQuestion) {
                            $visitorQuestion->update([
                                'status' => 'answered',
                                'admin_notes' => 'Answered using intelligent content analysis'
                            ]);
                        }

                        // Store intelligent answer in conversation history
                        ConversationMessage::addMessage($sessionId, 'ai', $intelligentAnswer);

                        return response()->json([
                            'success' => true,
                            'data' => [
                                'question' => $userQuestion,
                                'response' => $intelligentAnswer,
                                'type' => 'intelligent_answer',
                                'visitor_question_id' => $visitorQuestion ? $visitorQuestion->id : null
                            ]
                        ]);
                    }
                }

                // If no intelligent answer, try AI API based on settings
                $aiAnswer = $this->getAIResponseFromAPI($userQuestion, $aiSettings, $conversationHistory, $fullContext);

                if ($aiAnswer) {
                    // Store AI response in database for learning
                    $this->storeAIResponseForLearning($userQuestion, $aiAnswer);

                    // Update visitor question with AI answer
                    if ($visitorQuestion) {
                        $visitorQuestion->update([
                            'status' => 'answered',
                            'admin_notes' => 'Answered using AI API (ChatGPT/Gemini) - Stored for learning'
                        ]);
                    }

                    // Store AI response in conversation history
                    ConversationMessage::addMessage($sessionId, 'ai', $aiAnswer);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'question' => $userQuestion,
                            'response' => $aiAnswer,
                            'type' => 'ai_generated',
                            'visitor_question_id' => $visitorQuestion ? $visitorQuestion->id : null
                        ]
                    ]);
                }

                // Update visitor question with no match status
                if ($visitorQuestion) {
                    $visitorQuestion->update([
                        'status' => 'no_match',
                        'admin_notes' => 'No matching Q&A pair found and no intelligent answer generated'
                    ]);
                }

                // Generate contact suggestion with link
                $contactSuggestion = $this->generateContactSuggestion($request->question);

                return response()->json([
                    'success' => false,
                    'message' => 'No matching answer found for your question',
                    'suggestion' => $contactSuggestion,
                    'contact_link' => config('app.url') . '/contact',
                    'visitor_question_id' => $visitorQuestion ? $visitorQuestion->id : null
                ], 404);
            }

            // Update visitor question with matched Q&A
            if ($visitorQuestion) {
                $visitorQuestion->update([
                    'qa_pair_id' => $qaPair->id,
                    'status' => 'answered'
                ]);
            }

            // Increment usage count
            try {
                $qaPair->incrementUsage();
            } catch (\Exception $e) {
                Log::warning('Failed to increment usage count: ' . $e->getMessage());
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
                    'usage_count' => $qaPair->usage_count + 1,
                    'type' => 'qa_pair',
                    'visitor_question_id' => $visitorQuestion ? $visitorQuestion->id : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AI Response Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'I apologize, but I\'m having trouble processing your request right now. Please try again later or contact our support team.',
                'suggestion' => 'Please contact us through our contact page for immediate assistance.',
                'contact_link' => config('app.url') . '/contact'
            ], 500);
        }
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
     * Build conversation context for better understanding
     */
    private function buildConversationContext($conversationHistory, $currentQuestion)
    {
        $context = [
            'current_question' => $currentQuestion,
            'conversation_flow' => [],
            'user_intent' => $this->analyzeUserIntent($currentQuestion, $conversationHistory),
            'previous_topics' => $this->extractPreviousTopics($conversationHistory),
            'conversation_stage' => $this->determineConversationStage($conversationHistory)
        ];

        // Build conversation flow
        foreach ($conversationHistory as $message) {
            $context['conversation_flow'][] = [
                'sender' => $message['sender'],
                'message' => $message['message'],
                'timestamp' => $message['timestamp']
            ];
        }

        return $context;
    }

    /**
     * Analyze user intent based on conversation context
     */
    private function analyzeUserIntent($currentQuestion, $conversationHistory)
    {
        $question = strtolower($currentQuestion);
        $intent = 'general_inquiry';

        // Check for customer needs
        if ((strpos($question, 'i need') !== false || strpos($question, 'i want') !== false) &&
            (strpos($question, 'website') !== false || strpos($question, 'app') !== false)) {
            $intent = 'customer_need';
        }
        // Check for service inquiries
        elseif (strpos($question, 'service') !== false || strpos($question, 'what do you do') !== false) {
            $intent = 'service_inquiry';
        }
        // Check for pricing inquiries
        elseif (strpos($question, 'price') !== false || strpos($question, 'cost') !== false) {
            $intent = 'pricing_inquiry';
        }
        // Check for project discussions
        elseif (strpos($question, 'project') !== false || strpos($question, 'development') !== false) {
            $intent = 'project_discussion';
        }
        // Check for follow-up questions
        elseif (count($conversationHistory) > 0) {
            $intent = 'follow_up';
        }

        return $intent;
    }

    /**
     * Extract previous topics from conversation
     */
    private function extractPreviousTopics($conversationHistory)
    {
        $topics = [];
        foreach ($conversationHistory as $message) {
            if ($message['sender'] === 'visitor') {
                $question = strtolower($message['message']);
                if (strpos($question, 'website') !== false) $topics[] = 'website';
                if (strpos($question, 'app') !== false) $topics[] = 'mobile_app';
                if (strpos($question, 'design') !== false) $topics[] = 'design';
                if (strpos($question, 'price') !== false) $topics[] = 'pricing';
                if (strpos($question, 'service') !== false) $topics[] = 'services';
            }
        }
        return array_unique($topics);
    }

    /**
     * Determine conversation stage
     */
    private function determineConversationStage($conversationHistory)
    {
        $messageCount = count($conversationHistory);
        if ($messageCount === 0) return 'initial';
        if ($messageCount <= 2) return 'exploration';
        if ($messageCount <= 5) return 'discussion';
        return 'detailed_inquiry';
    }

    /**
     * Get quick answer for common questions with context
     */
    private function getQuickAnswer($question, $context = null)
    {
        $question = strtolower(trim($question));

        // Context-aware responses
        if ($context && isset($context['user_intent'])) {
            $intent = $context['user_intent'];

            // Handle follow-up questions with context
            if ($intent === 'follow_up' && isset($context['previous_topics'])) {
                $topics = $context['previous_topics'];
                if (in_array('website', $topics)) {
                    return "Based on our previous discussion about websites, I'd be happy to provide more specific information. What particular aspect of website development would you like to know more about?";
                }
                if (in_array('pricing', $topics)) {
                    return "For detailed pricing information tailored to your specific needs, I'd recommend contacting our team directly. We can provide customized quotes based on your project requirements.";
                }
            }
        }

        // Only keep essential time/date and conversation responses for AI learning
        $essentialAnswers = [
            // Exact time/date matches only
            'date' => 'Today is ' . now()->format('l, F j, Y'),
            'time' => 'Current time is ' . now()->format('g:i A'),
            'today' => 'Today is ' . now()->format('l, F j, Y'),
            'aj koto tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'aj ki tarik' => 'আজ ' . now()->format('j F, Y') . ' তারিখ',
            'current date' => 'Today is ' . now()->format('l, F j, Y'),
            'current time' => 'Current time is ' . now()->format('g:i A'),

            // Exact conversation acknowledgments only
            'okay' => 'Great! Is there anything else I can help you with?',
            'ok' => 'Perfect! Let me know if you need any other information.',
            'alright' => 'Excellent! Feel free to ask if you have more questions.',
            'got it' => 'Wonderful! I\'m here if you need anything else.',
            'understood' => 'Perfect! Don\'t hesitate to reach out for more help.',
            'thanks' => 'You\'re welcome! Happy to help anytime.',
            'thank you' => 'You\'re very welcome! Feel free to ask if you need anything else.',
            'bye' => 'Goodbye! Have a great day!',
            'goodbye' => 'Take care! Feel free to come back anytime.',
        ];

        // Use exact word matching instead of partial matching
        foreach ($essentialAnswers as $keyword => $answer) {
            // Check for exact word match (not partial)
            if (
                $question === $keyword ||
                preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $question)
            ) {
                return $answer;
            }
        }

        return null;
    }



    /**
     * Get website data from API (DISABLED to prevent infinite loops)
     */
    private function getWebsiteData()
    {
        // This method is disabled to prevent infinite loops
        return [
            'blogs' => [],
            'projects' => [],
            'team' => [],
            'about' => [],
            'pricing' => [],
        ];
    }

    /**
     * Fetch data from API (DISABLED to prevent infinite loops)
     */
    private function fetchApiData($url)
    {
        // This method is disabled to prevent infinite loops
        return [];
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
                $answer .= "\"" . $blog['title'] . "\", ";
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
                $answer .= "\"" . $project['title'] . "\", ";
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
                $answer .= $member['name'] . " (" . $member['position'] . "), ";
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
        return "I apologize, but I couldn't find a specific answer to your question in our knowledge base. Our team of experts would be happy to help you with this. Please contact us through our contact page at " . config('app.frontend_url', 'http://localhost:3000') . "/contact, and we'll get back to you with a detailed response. You can also call us directly for immediate assistance.";
    }

    /**
     * Generate intelligent answer from website content analysis
     */
    private function generateAnswerFromWebsiteData($userQuestion)
    {
        try {
            // Website content knowledge base
            $websiteContent = $this->getWebsiteContentKnowledge();

            // Analyze the question and find relevant content
            $answer = $this->analyzeQuestionAndGenerateAnswer($userQuestion, $websiteContent);

            if ($answer) {
                Log::info('Generated intelligent answer for question: ' . $userQuestion);
                return $answer;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error generating answer from website data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get comprehensive website content knowledge
     */
    private function getWebsiteContentKnowledge()
    {
        return [
            'company' => [
                'name' => 'BitsOfDev',
                'description' => 'We are BitsOfDev - a leading web development and digital agency',
                'founded' => '2019',
                'experience' => '5+ years',
                'projects' => '100+ projects delivered',
                'clients' => '50+ happy clients',
                'support' => '24/7 support available',
                'location' => 'Based in Bangladesh, serving clients worldwide',
                'specialization' => 'Website redesign, mobile optimization, SEO, and digital marketing'
            ],
            'services' => [
                'web_development' => 'We offer comprehensive web development services including responsive design, custom CMS integration, e-commerce functionality, and performance optimization',
                'website_redesign' => 'We specialize in modernizing existing websites with responsive design, improved user experience, and better performance',
                'mobile_optimization' => 'We fix mobile responsiveness issues and ensure your website works perfectly on all devices',
                'mobile_app' => 'We provide mobile app development for both iOS and Android platforms with modern UI/UX design',
                'ui_ux_design' => 'Our UI/UX design services focus on creating intuitive and beautiful user experiences',
                'seo_services' => 'We provide comprehensive SEO optimization services including keyword research, on-page optimization, and technical SEO',
                'digital_marketing' => 'We offer digital marketing services including social media marketing, Google Ads, and Facebook Ads',
                'consulting' => 'We offer digital consultation services to help businesses leverage technology for growth',
                'maintenance' => 'We provide ongoing website maintenance, security updates, and technical support',
                'hosting' => 'We offer reliable hosting solutions with 99.9% uptime guarantee'
            ],
            'common_issues' => [
                'mobile_responsive' => 'We fix websites that don\'t display properly on mobile devices',
                'slow_loading' => 'We optimize website speed and loading times for better performance',
                'seo_problems' => 'We improve search engine rankings and visibility',
                'security_issues' => 'We provide security audits, SSL certificates, and malware protection',
                'outdated_design' => 'We modernize old websites with contemporary design and functionality',
                'broken_functionality' => 'We fix broken links, forms, and other website functionality issues'
            ],
            'process' => [
                'discovery' => 'We start by understanding your business, goals, and requirements through detailed consultation',
                'planning' => 'We create a comprehensive project plan, timeline, and technical architecture',
                'development' => 'Our team brings your vision to life using cutting-edge technologies and best practices',
                'launch' => 'We ensure a smooth launch and provide ongoing support to help you succeed'
            ],
            'pricing' => [
                'website_redesign' => 'Website redesign starts from $299 and includes modern design, mobile responsiveness, and basic SEO',
                'seo_packages' => 'SEO packages start from $199/month for local businesses and $499/month for e-commerce sites',
                'digital_marketing' => 'Digital marketing services start from $399/month including social media management and Google Ads',
                'maintenance' => 'Website maintenance starts from $99/month including updates, backups, and security monitoring',
                'security_audit' => 'Security audit starts from $199 one-time fee with ongoing monitoring',
                'performance_optimization' => 'Performance optimization starts from $149 with guaranteed speed improvement'
            ],
            'team' => [
                'alex_chen' => 'Alex Chen is our Lead Developer with expertise in React, Node.js, and cloud technologies',
                'sarah_kim' => 'Sarah Kim is our UI/UX Designer passionate about creating intuitive and beautiful user experiences',
                'mike_rodriguez' => 'Mike Rodriguez is our Project Manager ensuring smooth delivery and client satisfaction'
            ],
            'technologies' => [
                'frontend' => 'We use modern frontend technologies including React, Next.js, Vue.js, and responsive design',
                'backend' => 'Our backend technologies include Laravel, Node.js, PHP, Python, and robust database systems',
                'cloud' => 'We work with cloud platforms like AWS, Google Cloud, and DigitalOcean for scalable solutions',
                'mobile' => 'For mobile development, we use React Native, Flutter, and native iOS/Android development',
                'seo_tools' => 'We use Google Analytics, Search Console, SEMrush, Ahrefs, and Yoast SEO',
                'marketing_tools' => 'We use Google Ads, Facebook Ads Manager, Mailchimp, and Hootsuite'
            ],
            'features' => [
                'fast' => 'We deliver lightning-fast, optimized solutions for speed and performance',
                'secure' => 'Enterprise-grade security with 99.9% uptime guarantee and regular backups',
                'support' => '24/7 dedicated support from our expert development team with project managers',
                'quality' => 'We maintain the highest standards in code quality, design, and project delivery',
                'mobile_first' => 'All our websites are mobile-first and responsive across all devices',
                'seo_optimized' => 'Every website we build is SEO-optimized for better search engine visibility'
            ],
            'industries' => [
                'restaurant' => 'We specialize in restaurant websites with online ordering, menu management, and table booking',
                'ecommerce' => 'We build powerful e-commerce websites with shopping carts, payment gateways, and inventory management',
                'business' => 'We create professional business websites with modern design and functionality',
                'corporate' => 'We develop corporate websites with company information, team pages, and service details',
                'portfolio' => 'We design portfolio websites to showcase work with interactive elements',
                'blog' => 'We build blog websites with CMS, SEO optimization, and social media integration'
            ]
        ];
    }

    /**
     * Analyze question and generate intelligent answer
     */
    private function analyzeQuestionAndGenerateAnswer($question, $content)
    {
        $question = strtolower($question);

        // Priority check for customer needs (override database)
        if ((strpos($question, 'i need') !== false || strpos($question, 'i want') !== false) && strpos($question, 'website') !== false) {
            return "Excellent! We'd love to help you create your website. To understand your specific requirements and provide the best solution, please contact our team. We can discuss your goals, features, design preferences, and budget. Visit our contact page to get started with a free consultation!";
        }

        // "Can you make" or "Do you make" type questions
        if ((strpos($question, 'can you make') !== false || strpos($question, 'do you make') !== false || strpos($question, 'make you') !== false) &&
            (strpos($question, 'website') !== false || strpos($question, 'app') !== false || strpos($question, 'mobile') !== false)
        ) {

            if (strpos($question, 'car wash') !== false) {
                return "Absolutely! We can create a professional car wash website for you. Our car wash websites include online booking system, service packages, customer management, payment integration, and mobile-responsive design. We can also add features like appointment scheduling, service pricing, customer reviews, and location finder. Contact our team to discuss your specific car wash business requirements!";
            } elseif (strpos($question, 'restaurant') !== false) {
                return "Yes! We can build a beautiful restaurant website for you. Our restaurant websites include online menu, table reservation system, food ordering, delivery integration, and customer reviews. We also add photo galleries, location finder, contact forms, and social media integration. Contact our team to discuss your restaurant's specific needs!";
            } elseif (strpos($question, 'ecommerce') !== false || strpos($question, 'online store') !== false) {
                return "Definitely! We can create a powerful e-commerce website for you. Our e-commerce solutions include product catalog, shopping cart, payment processing, inventory management, and order tracking. We also add mobile optimization, SEO features, and analytics integration. Contact our team to discuss your online store requirements!";
            } else {
                return "Yes, we can definitely create that for you! We specialize in building custom websites and mobile apps. To understand your specific requirements and provide the best solution, please contact our team. We can discuss your goals, features, design preferences, and budget. Visit our contact page to get started with a free consultation!";
            }
        }

        // Priority check for mobile app customer needs
        if ((strpos($question, 'i need') !== false || strpos($question, 'i want') !== false) && (strpos($question, 'mobile') !== false || strpos($question, 'app') !== false)) {
            return "Excellent! We'd love to help you create your mobile app. To understand your specific requirements and provide the best solution, please contact our team. We can discuss your app goals, features, platform preferences (iOS/Android), and budget. Visit our contact page to get started with a free consultation!";
        }

        // Company information questions
        if (strpos($question, 'company') !== false || strpos($question, 'bits') !== false || strpos($question, 'who are you') !== false) {
            return "We are BitsOfDev, a leading web development company founded in 2019. With 5+ years of experience, we've delivered 100+ projects to 50+ happy clients. We provide 24/7 support and are committed to delivering exceptional digital solutions.";
        }

        // Specific business type requests (car wash, restaurant, etc.)
        if (strpos($question, 'car wash') !== false || strpos($question, 'restaurant') !== false || strpos($question, 'ecommerce') !== false || strpos($question, 'booking') !== false || strpos($question, 'website for') !== false) {
            // Check if it's a specific business type request
            if (strpos($question, 'car wash') !== false) {
                return "Excellent! We'd love to help you create a car wash website. We can build features like online booking, service packages, customer management, payment integration, and mobile-responsive design. Our car wash websites include appointment scheduling, service pricing, customer reviews, and location finder. Contact our team to discuss your specific car wash business needs!";
            } elseif (strpos($question, 'restaurant') !== false) {
                return "Perfect! We specialize in restaurant websites with features like online menu, table reservation, food ordering, delivery integration, and customer reviews. Our restaurant websites include photo galleries, location finder, contact forms, and social media integration. Contact our team to discuss your restaurant's specific needs!";
            } elseif (strpos($question, 'ecommerce') !== false) {
                return "Great! We build powerful e-commerce websites with features like product catalog, shopping cart, payment processing, inventory management, and order tracking. Our e-commerce solutions include mobile optimization, SEO features, and analytics integration. Contact our team to discuss your online store requirements!";
            } else {
                return "Great! We'd love to help you create that website. To understand your specific requirements and provide the best solution, please contact our team. We can discuss features, design, and functionality tailored to your business needs. Visit our contact page to get started!";
            }
        }

        // Services questions (require intent words)
        if ((strpos($question, 'service') !== false || strpos($question, 'what do you do') !== false || strpos($question, 'offer') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false)
        ) {
            return "We offer comprehensive digital services including web development, mobile app development, UI/UX design, and digital consultation. Our services cover everything from responsive web design and custom CMS integration to e-commerce functionality and performance optimization.";
        }

        // Web development questions - customer need-focused
        if ((strpos($question, 'need') !== false || strpos($question, 'want') !== false || strpos($question, 'looking for') !== false) && strpos($question, 'website') !== false) {
            return "Excellent! We'd love to help you create your website. To understand your specific requirements and provide the best solution, please contact our team. We can discuss your goals, features, design preferences, and budget. Visit our contact page to get started with a free consultation!";
        }

        // General web development questions (require more context)
        if ((strpos($question, 'website') !== false || strpos($question, 'web development') !== false || strpos($question, 'web site') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'services') !== false)
        ) {
            return "We specialize in web development services including responsive design, custom CMS integration, e-commerce functionality, and performance optimization. Our websites are built with modern technologies like React and Next.js for optimal performance and user experience.";
        }

        // Mobile app questions (require more context)
        if ((strpos($question, 'mobile') !== false || strpos($question, 'app') !== false || strpos($question, 'ios') !== false || strpos($question, 'android') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'services') !== false || strpos($question, 'develop') !== false)
        ) {
            return "Yes, we provide mobile app development for both iOS and Android platforms. Our mobile development services include native app development with modern UI/UX design, ensuring your app delivers an exceptional user experience across all devices.";
        }

        // UI/UX design questions (require more context)
        if ((strpos($question, 'design') !== false || strpos($question, 'ui') !== false || strpos($question, 'ux') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'services') !== false)
        ) {
            return "Our UI/UX design services focus on creating intuitive and beautiful user experiences. Our team, led by Sarah Kim, is passionate about designing interfaces that are both functional and visually appealing, ensuring your users have the best possible experience.";
        }

        // Process questions (require intent words)
        if ((strpos($question, 'process') !== false || strpos($question, 'how do you work') !== false || strpos($question, 'workflow') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false)
        ) {
            return "Our proven process includes 4 key steps: 1) Discovery - understanding your business and requirements, 2) Planning - creating comprehensive project plans and architecture, 3) Development - bringing your vision to life with cutting-edge technologies, 4) Launch & Support - ensuring smooth deployment and ongoing support.";
        }

        // Pricing questions (require intent words)
        if ((strpos($question, 'price') !== false || strpos($question, 'cost') !== false || strpos($question, 'how much') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'much') !== false)
        ) {
            return "For detailed pricing information and custom quotes, please contact our team. We offer flexible pricing plans tailored to your specific needs. Visit our contact page or call us directly for a personalized quote.";
        }

        // Team questions (require intent words)
        if ((strpos($question, 'team') !== false || strpos($question, 'who works') !== false || strpos($question, 'developer') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'who') !== false)
        ) {
            return "Our talented team includes Alex Chen (Lead Developer with React/Node.js expertise), Sarah Kim (UI/UX Designer focused on beautiful user experiences), and Mike Rodriguez (Project Manager ensuring smooth delivery). We're a passionate team committed to delivering exceptional results.";
        }

        // Technology questions (require intent words)
        if ((strpos($question, 'technology') !== false || strpos($question, 'tech') !== false || strpos($question, 'framework') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false)
        ) {
            return "We use modern technologies including React, Next.js, Node.js, Python, and cloud platforms like AWS and Azure. We stay updated with the latest industry standards and choose the best technology stack for each project to ensure optimal performance and scalability.";
        }

        // Support questions (require intent words)
        if ((strpos($question, 'support') !== false || strpos($question, 'help') !== false || strpos($question, 'maintenance') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false)
        ) {
            return "We provide 24/7 dedicated support from our expert development team. Our support includes bug fixes, minor updates, security patches, and technical assistance. We also offer ongoing maintenance services to keep your project running smoothly.";
        }

        // Project/Portfolio questions (require intent words)
        if ((strpos($question, 'project') !== false || strpos($question, 'portfolio') !== false || strpos($question, 'work') !== false || strpos($question, 'example') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'show') !== false)
        ) {
            return "To see our portfolio and discuss your specific project requirements, please contact our team. We'd be happy to show you our previous work and discuss how we can help with your project. Visit our contact page for more information.";
        }

        // Contact questions (require intent words)
        if ((strpos($question, 'contact') !== false || strpos($question, 'reach') !== false || strpos($question, 'get in touch') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false || strpos($question, 'can i') !== false)
        ) {
            return "You can contact us through our contact form on the website, email us at hello@bitsofdev.com, or call us at +1 (555) 123-4567. We respond within 24 hours and offer free consultations to discuss your project needs.";
        }

        // Quality/Features questions (require intent words)
        if ((strpos($question, 'quality') !== false || strpos($question, 'fast') !== false || strpos($question, 'secure') !== false) &&
            (strpos($question, 'what') !== false || strpos($question, 'how') !== false || strpos($question, 'do you') !== false || strpos($question, 'can you') !== false)
        ) {
            return "We maintain the highest standards in code quality, design, and project delivery. Our solutions are optimized for speed and performance, include enterprise-grade security with 99.9% uptime guarantee, and come with regular backups and monitoring.";
        }

        // Handle single words or very short questions
        $wordCount = str_word_count($question);
        if ($wordCount <= 2) {
            // Check if it's a common single word that should get a helpful response
            $commonWords = ['website', 'mobile', 'app', 'design', 'ui', 'ux', 'price', 'cost', 'team', 'contact', 'help', 'service', 'project', 'portfolio'];
            $isCommonWord = false;
            $matchedWord = '';
            foreach ($commonWords as $word) {
                if (strpos($question, $word) !== false) {
                    $isCommonWord = true;
                    $matchedWord = $word;
                    break;
                }
            }

            if ($isCommonWord) {
                return "I'd be happy to help you with {$matchedWord}! Could you please ask a more specific question? For example, instead of just '{$question}', you could ask 'What {$matchedWord} services do you offer?' or 'How much does {$matchedWord} development cost?' This way I can give you a more detailed and helpful answer.";
            }
        }

        // Default response for unmatched questions
        return null;
    }

    /**
     * Get conversation history for context
     */
    private function getConversationHistoryForContext($sessionId, $limit = 5)
    {
        try {
            $session = ConversationSession::where('session_id', $sessionId)->first();
            if (!$session) {
                return [];
            }

            $messages = $session->recentMessages($limit);
            $history = [];

            foreach ($messages as $message) {
                $history[] = [
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'timestamp' => $message->created_at->format('H:i')
                ];
            }

            return $history;
        } catch (\Exception $e) {
            Log::error('Error getting conversation history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get AI response from external API (ChatGPT/Gemini)
     */
    private function getAIResponseFromAPI($question, $aiSettings = null, $conversationHistory = [], $fullContext = null)
    {
        try {
            // Use settings if provided, otherwise fall back to config
            if ($aiSettings) {
                $apiProvider = $aiSettings->ai_provider;
                $apiKey = config('app.ai_api_key');
            } else {
                $apiProvider = config('app.ai_provider', 'gemini');
                $apiKey = config('app.ai_api_key');
            }

            if (!$apiKey || $apiProvider === 'none') {
                Log::info('AI API not configured, skipping external AI call');
                return $this->getFallbackResponse($question, $fullContext);
            }

            // Check if training mode is enabled and we have enough learned responses
            if ($aiSettings && $aiSettings->training_mode) {
                $activeLearned = QAPair::where('category', 'ai_learned')->where('is_active', true)->count();
                if ($activeLearned >= $aiSettings->learning_threshold) {
                    Log::info('Training mode enabled with sufficient learned responses, using own AI');
                    return $this->getOwnAIResponse($question);
                }
            }

            // Create enhanced context for the AI
            $context = "You are BitsOfDev's AI assistant. BitsOfDev is a leading software development agency specializing in web development, mobile apps, marketing, and SEO services.

            SERVICES WE PROVIDE:
            - Website Redesign & Modernization (Modern design, mobile responsiveness, user experience improvement)
            - Mobile Responsive Fixes (Fix existing website mobile display issues, ensure perfect mobile experience)
            - SEO Optimization Services (Keyword research, on-page optimization, technical SEO, content marketing)
            - Digital Marketing Services (Social media marketing, Google Ads, Facebook Ads, email marketing)
            - Website Security Updates (Security audits, SSL certificates, malware removal, regular security updates)
            - Performance Optimization (Website speed improvement, loading time optimization, performance enhancement)
            - Website Maintenance (Ongoing updates, backups, technical support, monitoring)
            - Restaurant Website Development (Online ordering, Menu management, Table booking, Delivery tracking, Customer reviews)
            - E-commerce Website Development (Shopping cart, Payment gateways, Inventory management, Order tracking, Customer accounts)
            - Business Website Development (Professional design, Contact forms, Service pages, Portfolio sections, SEO optimization)
            - Corporate Website Development (Company information, Team pages, Service details, Modern functionality)
            - Portfolio Website Development (Showcase work, Interactive elements, Modern design)
            - Blog Website Development (CMS, SEO optimization, Social media integration)
            - Mobile App Development (iOS, Android, Cross-platform, Modern UI/UX)
            - UI/UX Design Services
            - Digital Consultation Services

            COMMON WEBSITE ISSUES WE SOLVE:
            - Mobile responsiveness problems (websites not displaying properly on mobile devices)
            - Slow loading websites (performance optimization and speed improvement)
            - Poor SEO rankings (comprehensive SEO optimization and strategy)
            - Security vulnerabilities (security audits, SSL implementation, malware protection)
            - Outdated designs (modern website redesign and user experience improvement)
            - No analytics tracking (Google Analytics setup and tracking implementation)
            - Broken links and functionality issues (website maintenance and fixes)
            - Poor user experience (navigation improvement and design optimization)

            TECHNOLOGIES WE USE:
            - Frontend: React, Next.js, Vue.js, Angular, HTML5, CSS3, JavaScript
            - Backend: Laravel, Node.js, PHP, Python, Express.js
            - Mobile: React Native, Flutter, Swift, Kotlin
            - Database: MySQL, PostgreSQL, MongoDB
            - Cloud: AWS, Google Cloud, DigitalOcean
            - SEO Tools: Google Analytics, Search Console, SEMrush, Ahrefs, Yoast SEO
            - Marketing Tools: Google Ads, Facebook Ads Manager, Mailchimp, Hootsuite
            - Security Tools: SSL certificates, malware scanners, firewalls, security audits

            OUR EXPERTISE:
            - We have completed 100+ projects across various industries
            - Specialized in website redesign, mobile optimization, SEO, and digital marketing
            - Modern, responsive, and mobile-friendly designs
            - Comprehensive SEO optimization and performance enhancement
            - 24/7 support and ongoing maintenance services
            - Full-service digital agency providing development, marketing, and SEO solutions

            PRICING INFORMATION:
            - Website redesign: Starting from $299 (includes modern design, mobile responsiveness, basic SEO)
            - SEO packages: $199/month for local businesses, $499/month for e-commerce sites
            - Digital marketing: Starting from $399/month (social media management, Google Ads)
            - Website maintenance: Starting from $99/month (updates, backups, security monitoring)
            - Security audit: Starting from $199 one-time fee with ongoing monitoring
            - Performance optimization: Starting from $149 with guaranteed speed improvement

            RESPONSE GUIDELINES:
            - Be helpful, professional, and informative
            - Provide specific details about our services when relevant
            - Keep responses concise but comprehensive (2-4 sentences)
            - For pricing, complex projects, or detailed consultations, suggest contacting our team
            - Always mention our expertise and experience when relevant
            - If someone asks about website issues (mobile responsive, SEO, security, speed), provide detailed information about our solutions
            - Emphasize that we help with existing websites, not just new ones
            - Mention that many clients come to us with existing websites that need improvements

            CONVERSATION CONTEXT:
            - If user says 'okay', 'ok', 'alright', 'got it', 'understood' - respond with acknowledgment like 'Great! Is there anything else I can help you with?'
            - If user says 'thanks', 'thank you' - respond with 'You're welcome! Feel free to ask if you need anything else.'
            - If user says 'bye', 'goodbye' - respond with 'Goodbye! Have a great day!'
            - Don't provide service information for conversation acknowledgments";

            // Add conversation history to context
            if (!empty($conversationHistory)) {
                $historyText = "\n\nConversation History:\n";
                foreach ($conversationHistory as $msg) {
                    $sender = $msg['sender'] === 'visitor' ? 'User' : 'Assistant';
                    $historyText .= "{$sender}: {$msg['message']}\n";
                }
                $context .= $historyText;
            }

            if ($apiProvider === 'openai') {
                return $this->getOpenAIResponse($question, $context, $apiKey);
            } elseif ($apiProvider === 'gemini') {
                return $this->getGeminiResponse($question, $context, $apiKey, $fullContext);
            }

            return $this->getFallbackResponse($question, $fullContext);
        } catch (\Exception $e) {
            Log::error('Error getting AI response from API: ' . $e->getMessage());
            return $this->getFallbackResponse($question, $fullContext);
        }
    }

    /**
     * Get response from own AI (learned responses)
     */
    private function getOwnAIResponse($question)
    {
        try {
            // Find best matching learned response
            $learnedResponse = QAPair::where('category', 'ai_learned')
                ->where('is_active', true)
                ->get()
                ->first(function ($qa) use ($question) {
                    $questionLower = strtolower(trim($question));
                    $qaQuestion = strtolower(trim($qa->question));

                    // Calculate similarity
                    $questionWords = explode(' ', $questionLower);
                    $qaWords = explode(' ', $qaQuestion);

                    $commonWords = array_intersect($questionWords, $qaWords);
                    $similarity = count($commonWords) / max(count($questionWords), count($qaWords));

                    return $similarity >= 0.3; // 30% similarity threshold
                });

            if ($learnedResponse) {
                Log::info('Using own AI response for question: ' . $question);
                return $learnedResponse->answer_1;
            }

            // If no learned response found, fall back to external AI
            Log::info('No learned response found, falling back to external AI');
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting own AI response: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get response from OpenAI ChatGPT API
     */
    private function getOpenAIResponse($question, $context, $apiKey)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $context
                        ],
                        [
                            'role' => 'user',
                            'content' => $question
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7
                ],
                'timeout' => 10
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['choices'][0]['message']['content'])) {
                $response = trim($data['choices'][0]['message']['content']);
                return $this->shortenResponse($response);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get response from Google Gemini API with retry mechanism
     */
    private function getGeminiResponse($question, $context, $apiKey, $fullContext = null)
    {
        $maxRetries = 3;
        $retryDelay = 1; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Check if we already have a response for this exact question
                $existingResponse = QAPair::where('question', $question)
                    ->where('category', 'ai_learned')
                    ->where('is_active', true)
                    ->first();

                if ($existingResponse) {
                    Log::info('Using cached Gemini response for question: ' . $question);
                    return $existingResponse->answer_1;
                }

                $client = new \GuzzleHttp\Client([
                    'timeout' => 15, // Increased timeout
                    'connect_timeout' => 10
                ]);

                $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $context . "\n\nUser Question: " . $question
                                    ]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'maxOutputTokens' => 150, // Increased for better responses
                            'temperature' => 0.1,  // Lower temperature for more consistent responses
                            'topP' => 0.8,        // More focused responses
                            'topK' => 20          // Limit vocabulary for consistency
                        ]
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $response = trim($data['candidates'][0]['content']['parts'][0]['text']);
                    Log::info('Gemini API success on attempt ' . $attempt);
                    return $this->shortenResponse($response);
                }

                // If no response but no error, try fallback
                if ($attempt === $maxRetries) {
                    Log::warning('Gemini API returned no response after ' . $maxRetries . ' attempts');
                    return $this->getFallbackResponse($question, $fullContext);
                }

            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                Log::warning('Gemini API connection error on attempt ' . $attempt . ': ' . $e->getMessage());
                if ($attempt === $maxRetries) {
                    return $this->getFallbackResponse($question, $fullContext);
                }
                sleep($retryDelay * $attempt); // Exponential backoff
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::warning('Gemini API request error on attempt ' . $attempt . ': ' . $e->getMessage());
                if ($attempt === $maxRetries) {
                    return $this->getFallbackResponse($question, $fullContext);
                }
                sleep($retryDelay * $attempt);
            } catch (\Exception $e) {
                Log::error('Gemini API unexpected error on attempt ' . $attempt . ': ' . $e->getMessage());
                if ($attempt === $maxRetries) {
                    return $this->getFallbackResponse($question, $fullContext);
                }
                sleep($retryDelay * $attempt);
            }
        }

        return $this->getFallbackResponse($question, $fullContext);
    }

    /**
     * Shorten AI response to keep it concise
     */
    private function shortenResponse($response)
    {
        // If response is too long, truncate it
        if (strlen($response) > 200) {
            $response = substr($response, 0, 200);
            // Find the last complete sentence
            $lastPeriod = strrpos($response, '.');
            if ($lastPeriod !== false) {
                $response = substr($response, 0, $lastPeriod + 1);
            }
        }

        // Add contact suggestion for business-related responses
        $businessKeywords = ['price', 'cost', 'project', 'quote', 'business', 'consultation', 'website', 'service', 'need', 'want', 'create', 'build', 'develop'];
        $responseLower = strtolower($response);

        foreach ($businessKeywords as $keyword) {
            if (strpos($responseLower, $keyword) !== false) {
                $response .= " For detailed information, please contact our team.";
                break;
            }
        }

        return $response;
    }

    /**
     * Get fallback response when AI APIs fail
     */
    private function getFallbackResponse($question, $fullContext = null)
    {
        $question = strtolower($question);

        // Use context to provide better fallback responses
        if ($fullContext && isset($fullContext['user_intent'])) {
            $intent = $fullContext['user_intent'];

            switch ($intent) {
                case 'customer_need':
                    return "I understand you're looking for website or app development services. While I'm experiencing some technical difficulties, I'd be happy to connect you with our team for a detailed discussion about your project needs. Please contact us through our contact page for immediate assistance.";

                case 'service_inquiry':
                    return "We offer comprehensive web development, mobile app development, UI/UX design, and digital marketing services. For detailed information about our services, please contact our team directly through our contact page.";

                case 'pricing_inquiry':
                    return "For accurate pricing information tailored to your specific project requirements, please contact our team directly. We provide customized quotes based on your needs and budget.";

                case 'project_discussion':
                    return "I'd love to discuss your project in detail. Please contact our team through our contact page, and we'll schedule a consultation to understand your requirements better.";

                case 'follow_up':
                    return "I'm experiencing some technical difficulties right now, but I want to make sure you get the information you need. Please contact our team directly for immediate assistance.";

                default:
                    return "I'm currently experiencing some technical difficulties, but I'm here to help. Please contact our team directly through our contact page for immediate assistance with your inquiry.";
            }
        }

        // Default fallback responses based on keywords
        if (strpos($question, 'website') !== false || strpos($question, 'web') !== false) {
            return "We specialize in website development and redesign services. For detailed information about our web development capabilities, please contact our team directly.";
        }

        if (strpos($question, 'app') !== false || strpos($question, 'mobile') !== false) {
            return "We provide mobile app development services for both iOS and Android. Contact our team for detailed information about our mobile development capabilities.";
        }

        if (strpos($question, 'price') !== false || strpos($question, 'cost') !== false) {
            return "For accurate pricing information, please contact our team directly. We provide customized quotes based on your specific project requirements.";
        }

        // Generic fallback
        return "I'm currently experiencing some technical difficulties, but I'm here to help. Please contact our team directly through our contact page for immediate assistance with your inquiry.";
    }

    /**
     * Store AI response in database for learning purposes
     */
    private function storeAIResponseForLearning($question, $answer)
    {
        try {
            // Check if this Q&A pair already exists
            $existingPair = QAPair::where('question', $question)
                ->where('answer_1', $answer)
                ->first();

            if (!$existingPair) {
                // Create new Q&A pair for learning
                QAPair::create([
                    'question' => $question,
                    'answer_1' => $answer,
                    'category' => 'ai_learned',
                    'is_active' => true, // Auto-activate Gemini responses - they are reliable
                    'usage_count' => 0
                ]);

                Log::info('AI response stored for learning: ' . $question);

                // Also store conversation context for better learning
                $this->storeConversationContext($question, $answer);
            } else {
                // Increment usage count for existing learned response
                $existingPair->incrementUsage();
            }
        } catch (\Exception $e) {
            Log::error('Error storing AI response for learning: ' . $e->getMessage());
        }
    }

    /**
     * Store conversation context for better AI learning
     */
    private function storeConversationContext($question, $answer)
    {
        try {
            // Store additional context about the conversation
            $contextData = [
                'question_type' => $this->categorizeQuestion($question),
                'answer_quality' => $this->assessAnswerQuality($answer),
                'timestamp' => now(),
                'source' => 'ai_generated'
            ];

            // You can extend this to store more context in a separate table if needed
            Log::info('Conversation context stored: ' . json_encode($contextData));
        } catch (\Exception $e) {
            Log::error('Error storing conversation context: ' . $e->getMessage());
        }
    }

    /**
     * Categorize question for better learning
     */
    private function categorizeQuestion($question)
    {
        $question = strtolower($question);

        if (strpos($question, 'website') !== false) return 'website_related';
        if (strpos($question, 'app') !== false || strpos($question, 'mobile') !== false) return 'mobile_related';
        if (strpos($question, 'price') !== false || strpos($question, 'cost') !== false) return 'pricing_related';
        if (strpos($question, 'service') !== false) return 'service_related';
        if (strpos($question, 'project') !== false) return 'project_related';
        if (strpos($question, 'design') !== false) return 'design_related';
        if (strpos($question, 'seo') !== false) return 'seo_related';

        return 'general_inquiry';
    }

    /**
     * Assess answer quality for learning improvement
     */
    private function assessAnswerQuality($answer)
    {
        $quality = 'good';

        // Check answer length and completeness
        if (strlen($answer) < 50) $quality = 'short';
        if (strlen($answer) > 500) $quality = 'long';

        // Check for contact suggestions (indicates incomplete answer)
        if (strpos($answer, 'contact') !== false && strpos($answer, 'team') !== false) {
            $quality = 'requires_contact';
        }

        return $quality;
    }

    /**
     * Get AI learning statistics
     */
    public function getAILearningStats()
    {
        try {
            $totalLearned = QAPair::where('category', 'ai_learned')->count();
            $activeLearned = QAPair::where('category', 'ai_learned')->where('is_active', true)->count();
            $pendingReview = QAPair::where('category', 'ai_learned')->where('is_active', false)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_learned' => $totalLearned,
                    'active_learned' => $activeLearned,
                    'pending_review' => $pendingReview,
                    'learning_progress' => $totalLearned > 0 ? round(($activeLearned / $totalLearned) * 100, 2) : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting AI learning stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate learned AI responses (for when AI is ready to publish)
     */
    public function activateLearnedResponses()
    {
        try {
            $updated = QAPair::where('category', 'ai_learned')
                ->where('is_active', false)
                ->update(['is_active' => true]);

            Log::info('Activated ' . $updated . ' learned AI responses');

            return response()->json([
                'success' => true,
                'message' => 'Successfully activated ' . $updated . ' learned AI responses',
                'activated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error activating learned responses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation history for a session
     */
    public function getConversationHistory($sessionId)
    {
        try {
            $session = ConversationSession::where('session_id', $sessionId)->first();

            if (!$session) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'messages' => [],
                        'session_info' => null
                    ]
                ]);
            }

            $messages = $session->messages()->orderBy('created_at')->get();
            $formattedMessages = $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'timestamp' => $message->created_at->format('H:i:s'),
                    'date' => $message->created_at->format('Y-m-d')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $formattedMessages,
                    'session_info' => [
                        'session_id' => $session->session_id,
                        'message_count' => $session->message_count,
                        'last_activity' => $session->last_activity,
                        'created_at' => $session->created_at
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting conversation history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear conversation history for a session
     */
    public function clearConversationHistory($sessionId)
    {
        try {
            $session = ConversationSession::where('session_id', $sessionId)->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Delete all messages for this session
            $session->messages()->delete();

            // Reset session message count
            $session->update([
                'message_count' => 0,
                'last_activity' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation history cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing conversation history: ' . $e->getMessage()
            ], 500);
        }
    }
}
