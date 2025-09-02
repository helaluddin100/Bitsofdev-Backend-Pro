<?php

// Test AI API endpoint
$url = 'http://localhost:8000/api/chat/ai-response';
$data = json_encode(['question' => 'Hello, how are you?']);

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => $data
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Response: " . $result . "\n";

if ($result === false) {
    echo "Error: " . error_get_last()['message'] . "\n";
}
