<?php

namespace App\Http\Controllers;

use App\Models\QAPair;
use App\Models\VisitorQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

            // Check for quick answers first
            $quickAnswer = $this->getQuickAnswer($userQuestion);
            if ($quickAnswer) {
                if ($visitorQuestion) {
                    $visitorQuestion->update([
                        'answer' => $quickAnswer,
                        'status' => 'answered',
                        'admin_notes' => 'Quick answer provided'
                    ]);
                }

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

            // Find matching Q&A pair with timeout protection
            $qaPair = null;
            try {
                $qaPair = QAPair::where('is_active', true)
                    ->get()
                    ->first(function ($qa) use ($userQuestion) {
                        $question = strtolower(trim($qa->question));
                        return str_contains($userQuestion, $question) || str_contains($question, $userQuestion);
                    });
            } catch (\Exception $e) {
                Log::warning('Error searching Q&A pairs: ' . $e->getMessage());
            }

            if (!$qaPair) {
                // Try to generate intelligent answer from website content
                $intelligentAnswer = $this->generateAnswerFromWebsiteData($userQuestion);

                if ($intelligentAnswer) {
                    // Update visitor question with intelligent answer
                    if ($visitorQuestion) {
                        $visitorQuestion->update([
                            'status' => 'answered',
                            'admin_notes' => 'Answered using intelligent content analysis'
                        ]);
                    }

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

                // If no intelligent answer, try AI API (ChatGPT/Gemini)
                $aiAnswer = $this->getAIResponseFromAPI($userQuestion);

                if ($aiAnswer) {
                    // Update visitor question with AI answer
                    if ($visitorQuestion) {
                        $visitorQuestion->update([
                            'status' => 'answered',
                            'admin_notes' => 'Answered using AI API (ChatGPT/Gemini)'
                        ]);
                    }

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
        return "I apologize, but I couldn't find a specific answer to your question in our knowledge base. Our team of experts would be happy to help you with this. Please contact us through our contact page, and we'll get back to you with a detailed response. You can also call us directly for immediate assistance.";
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
                'description' => 'We are BitsOfDev - a leading web development company',
                'founded' => '2019',
                'experience' => '5+ years',
                'projects' => '100+ projects delivered',
                'clients' => '50+ happy clients',
                'support' => '24/7 support available'
            ],
            'services' => [
                'web_development' => 'We offer web development services including responsive design, custom CMS integration, e-commerce functionality, and performance optimization',
                'mobile_app' => 'We provide mobile app development for both iOS and Android platforms with modern UI/UX design',
                'ui_ux_design' => 'Our UI/UX design services focus on creating intuitive and beautiful user experiences',
                'consulting' => 'We offer digital consultation services to help businesses leverage technology for growth',
                'seo' => 'We provide SEO optimization services to improve your website\'s search engine rankings',
                'hosting' => 'We offer web development, mobile app development, UI/UX design, and digital consultation services'
            ],
            'process' => [
                'discovery' => 'We start by understanding your business, goals, and requirements through detailed consultation',
                'planning' => 'We create a comprehensive project plan, timeline, and technical architecture',
                'development' => 'Our team brings your vision to life using cutting-edge technologies and best practices',
                'launch' => 'We ensure a smooth launch and provide ongoing support to help you succeed'
            ],
            'pricing' => [
                'starter' => 'Starter plan starts at $999/month and includes responsive web design, up to 5 pages, basic SEO, contact form integration, and 30 days support',
                'professional' => 'Professional plan is $2499/month and includes everything in Starter plus up to 15 pages, custom CMS, e-commerce functionality, advanced SEO, and 90 days support',
                'enterprise' => 'Enterprise plan is $4999/month and includes unlimited pages, custom web applications, API development, database design, and 6 months support'
            ],
            'team' => [
                'alex_chen' => 'Alex Chen is our Lead Developer with expertise in React, Node.js, and cloud technologies',
                'sarah_kim' => 'Sarah Kim is our UI/UX Designer passionate about creating intuitive and beautiful user experiences',
                'mike_rodriguez' => 'Mike Rodriguez is our Project Manager ensuring smooth delivery and client satisfaction'
            ],
            'technologies' => [
                'frontend' => 'We use modern frontend technologies including React, Next.js, and responsive design',
                'backend' => 'Our backend technologies include Node.js, Python, and robust database systems',
                'cloud' => 'We work with cloud platforms like AWS and Azure for scalable solutions',
                'mobile' => 'For mobile development, we use native iOS and Android development approaches'
            ],
            'features' => [
                'fast' => 'We deliver lightning-fast, optimized solutions for speed and performance',
                'secure' => 'Enterprise-grade security with 99.9% uptime guarantee and regular backups',
                'support' => '24/7 dedicated support from our expert development team with project managers',
                'quality' => 'We maintain the highest standards in code quality, design, and project delivery'
            ]
        ];
    }

    /**
     * Analyze question and generate intelligent answer
     */
    private function analyzeQuestionAndGenerateAnswer($question, $content)
    {
        $question = strtolower($question);

        // Company information questions
        if (strpos($question, 'company') !== false || strpos($question, 'bits') !== false || strpos($question, 'who are you') !== false) {
            return "We are BitsOfDev, a leading web development company founded in 2019. With 5+ years of experience, we've delivered 100+ projects to 50+ happy clients. We provide 24/7 support and are committed to delivering exceptional digital solutions.";
        }

        // Services questions
        if (strpos($question, 'service') !== false || strpos($question, 'what do you do') !== false || strpos($question, 'offer') !== false) {
            return "We offer comprehensive digital services including web development, mobile app development, UI/UX design, and digital consultation. Our services cover everything from responsive web design and custom CMS integration to e-commerce functionality and performance optimization.";
        }

        // Web development questions
        if (strpos($question, 'website') !== false || strpos($question, 'web development') !== false || strpos($question, 'web site') !== false) {
            return "We specialize in web development services including responsive design, custom CMS integration, e-commerce functionality, and performance optimization. Our websites are built with modern technologies like React and Next.js for optimal performance and user experience.";
        }

        // Mobile app questions
        if (strpos($question, 'mobile') !== false || strpos($question, 'app') !== false || strpos($question, 'ios') !== false || strpos($question, 'android') !== false) {
            return "Yes, we provide mobile app development for both iOS and Android platforms. Our mobile development services include native app development with modern UI/UX design, ensuring your app delivers an exceptional user experience across all devices.";
        }

        // UI/UX design questions
        if (strpos($question, 'design') !== false || strpos($question, 'ui') !== false || strpos($question, 'ux') !== false) {
            return "Our UI/UX design services focus on creating intuitive and beautiful user experiences. Our team, led by Sarah Kim, is passionate about designing interfaces that are both functional and visually appealing, ensuring your users have the best possible experience.";
        }

        // Process questions
        if (strpos($question, 'process') !== false || strpos($question, 'how do you work') !== false || strpos($question, 'workflow') !== false) {
            return "Our proven process includes 4 key steps: 1) Discovery - understanding your business and requirements, 2) Planning - creating comprehensive project plans and architecture, 3) Development - bringing your vision to life with cutting-edge technologies, 4) Launch & Support - ensuring smooth deployment and ongoing support.";
        }

        // Pricing questions
        if (strpos($question, 'price') !== false || strpos($question, 'cost') !== false || strpos($question, 'how much') !== false) {
            return "Our pricing plans start at $999/month for the Starter plan, $2499/month for Professional, and $4999/month for Enterprise. Each plan includes different features and support levels. We also offer yearly plans with 17% discount. Contact us for a detailed quote tailored to your specific needs.";
        }

        // Team questions
        if (strpos($question, 'team') !== false || strpos($question, 'who works') !== false || strpos($question, 'developer') !== false) {
            return "Our talented team includes Alex Chen (Lead Developer with React/Node.js expertise), Sarah Kim (UI/UX Designer focused on beautiful user experiences), and Mike Rodriguez (Project Manager ensuring smooth delivery). We're a passionate team committed to delivering exceptional results.";
        }

        // Technology questions
        if (strpos($question, 'technology') !== false || strpos($question, 'tech') !== false || strpos($question, 'framework') !== false) {
            return "We use modern technologies including React, Next.js, Node.js, Python, and cloud platforms like AWS and Azure. We stay updated with the latest industry standards and choose the best technology stack for each project to ensure optimal performance and scalability.";
        }

        // Support questions
        if (strpos($question, 'support') !== false || strpos($question, 'help') !== false || strpos($question, 'maintenance') !== false) {
            return "We provide 24/7 dedicated support from our expert development team. Our support includes bug fixes, minor updates, security patches, and technical assistance. We also offer ongoing maintenance services to keep your project running smoothly.";
        }

        // Contact questions
        if (strpos($question, 'contact') !== false || strpos($question, 'reach') !== false || strpos($question, 'get in touch') !== false) {
            return "You can contact us through our contact form on the website, email us at hello@bitsofdev.com, or call us at +1 (555) 123-4567. We respond within 24 hours and offer free consultations to discuss your project needs.";
        }

        // Quality/Features questions
        if (strpos($question, 'quality') !== false || strpos($question, 'fast') !== false || strpos($question, 'secure') !== false) {
            return "We maintain the highest standards in code quality, design, and project delivery. Our solutions are optimized for speed and performance, include enterprise-grade security with 99.9% uptime guarantee, and come with regular backups and monitoring.";
        }

        // Default response for unmatched questions
        return null;
    }

    /**
     * Get AI response from external API (ChatGPT/Gemini)
     */
    private function getAIResponseFromAPI($question)
    {
        try {
            // Get API configuration
            $apiProvider = config('app.ai_provider', 'openai'); // openai, gemini, or none
            $apiKey = config('app.ai_api_key');

            if (!$apiKey || $apiProvider === 'none') {
                Log::info('AI API not configured, skipping external AI call');
                return null;
            }

            // Create context for the AI
            $context = "You are a helpful assistant for BitsOfDev, a web development company. ";
            $context .= "We offer web development, mobile app development, UI/UX design, and digital consultation services. ";
            $context .= "We have 5+ years of experience and have delivered 100+ projects to 50+ happy clients. ";
            $context .= "Please provide a helpful, professional response to the user's question. ";
            $context .= "If the question is not related to our services, still try to be helpful but mention that we specialize in web development services.";

            if ($apiProvider === 'openai') {
                return $this->getOpenAIResponse($question, $context, $apiKey);
            } elseif ($apiProvider === 'gemini') {
                return $this->getGeminiResponse($question, $context, $apiKey);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting AI response from API: ' . $e->getMessage());
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
                    'max_tokens' => 200,
                    'temperature' => 0.7
                ],
                'timeout' => 10
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['choices'][0]['message']['content'])) {
                return trim($data['choices'][0]['message']['content']);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get response from Google Gemini API
     */
    private function getGeminiResponse($question, $context, $apiKey)
    {
        try {
            $client = new \GuzzleHttp\Client();

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
                        'maxOutputTokens' => 200,
                        'temperature' => 0.7
                    ]
                ],
                'timeout' => 10
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($data['candidates'][0]['content']['parts'][0]['text']);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            return null;
        }
    }
}
