<?php
/**
 * Gemini API Test Script
 *
 * This script tests if your Gemini API key is working correctly
 *
 * Usage: php test_gemini_api.php
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel to use config
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get API key from config (which reads from .env)
$apiKey = config('app.ai_api_key', '');

if (empty($apiKey)) {
    echo "❌ ERROR: AI_API_KEY not found in .env file\n";
    echo "Please add AI_API_KEY=your-api-key-here to your .env file\n";
    exit(1);
}

// Validate API key format
if (strlen($apiKey) < 35 || !str_starts_with($apiKey, 'AIza')) {
    echo "❌ ERROR: Invalid API key format\n";
    echo "API key should start with 'AIza' and be at least 35 characters\n";
    echo "Your key starts with: " . substr($apiKey, 0, 10) . "...\n";
    echo "Your key length: " . strlen($apiKey) . " characters\n";
    exit(1);
}

echo "✅ API Key found and format looks valid\n";
echo "Key prefix: " . substr($apiKey, 0, 10) . "...\n";
echo "Key length: " . strlen($apiKey) . " characters\n\n";

// Test API call
echo "Testing Gemini API connection...\n";

$client = new \GuzzleHttp\Client([
    'timeout' => 20,
    'connect_timeout' => 15
]);

try {
    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . urlencode($apiKey);

    echo "Sending request to Gemini API...\n";

    $response = $client->post($apiUrl, [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'contents' => [[
                'parts' => [[
                    'text' => 'Hello! you know sparkedev?.'
                ]]
            ]],
            'generationConfig' => [
                'maxOutputTokens' => 50,
                'temperature' => 0.1
            ]
        ]
    ]);

    $statusCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();
    $data = json_decode($responseBody, true);

    echo "✅ API Response received (Status: $statusCode)\n\n";

    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $responseText = trim($data['candidates'][0]['content']['parts'][0]['text']);
        echo "✅ SUCCESS! Gemini API is working correctly!\n";
        echo "Response: " . $responseText . "\n\n";
        echo "🎉 Your Gemini integration should work now!\n";
        exit(0);
    } else {
        echo "❌ ERROR: Unexpected response format\n";
        echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        exit(1);
    }

} catch (\GuzzleHttp\Exception\ConnectException $e) {
    echo "❌ ERROR: Connection failed\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Please check your internet connection\n";
    exit(1);

} catch (\GuzzleHttp\Exception\RequestException $e) {
    $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'unknown';
    $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'no response';

    echo "❌ ERROR: API request failed\n";
    echo "Status Code: $statusCode\n";
    echo "Message: " . $e->getMessage() . "\n";

    if ($statusCode == 401 || $statusCode == 403) {
        echo "\n⚠️  Authentication Error - Your API key might be invalid or expired\n";
        echo "Please verify your API key at: https://aistudio.google.com/app/apikey\n";
    }

    echo "\nResponse: " . substr($responseBody, 0, 500) . "\n";
    exit(1);

} catch (\Exception $e) {
    echo "❌ ERROR: Unexpected error\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Type: " . get_class($e) . "\n";
    exit(1);
}

