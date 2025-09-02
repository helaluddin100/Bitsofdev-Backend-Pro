<?php

/**
 * Test script for the AI Chatbot
 * Run this script to test if the chatbot is working properly
 *
 * Usage: php test_chatbot.php
 */

// Test questions
$testQuestions = [
    "Hello",
    "What services do you offer?",
    "How much does a website cost?",
    "What's your process?",
    "Can I see your portfolio?",
    "What is the current date?",
    "Tell me about your team",
    "What technologies do you use?",
    "How long does it take to build a website?",
    "Do you provide maintenance services?",
    "What is your company name?",
    "How can I contact you?",
    "Do you offer mobile app development?",
    "What is your pricing structure?",
    "Can you help with SEO?",
    "Do you provide hosting services?",
    "What is your refund policy?",
    "Do you work with startups?",
    "What is your development process?",
    "Can you integrate with third-party APIs?"
];

// API endpoint
$apiUrl = 'http://localhost:8000/api/chat/ai-response';

echo "🤖 Testing AI Chatbot\n";
echo "====================\n\n";

foreach ($testQuestions as $index => $question) {
    echo "Test " . ($index + 1) . ": " . $question . "\n";
    echo "Response: ";

    // Make API request
    $data = json_encode(['question' => $question]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        echo "❌ Request failed\n";
    } else {
        $result = json_decode($response, true);

        if ($httpCode === 200 && isset($result['success']) && $result['success']) {
            echo "✅ " . $result['data']['response'] . "\n";
        } elseif ($httpCode === 404 && isset($result['suggestion'])) {
            echo "⚠️  " . $result['suggestion'] . "\n";
        } else {
            echo "❌ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";

    // Small delay between requests
    sleep(1);
}

echo "🎉 Testing completed!\n";
echo "If you see mostly ✅ responses, your chatbot is working well.\n";
echo "If you see ⚠️ responses, those are expected for questions not in your database.\n";
echo "If you see ❌ responses, there might be an issue with your backend.\n";
