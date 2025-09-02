$body = @{
    question = "Hello, how are you?"
} | ConvertTo-Json

$headers = @{
    "Content-Type" = "application/json"
}

try {
    $response = Invoke-RestMethod -Uri "http://localhost:8000/api/chat/ai-response" -Method POST -Body $body -Headers $headers
    Write-Host "Success: $($response | ConvertTo-Json -Depth 3)"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
    Write-Host "Response: $($_.Exception.Response)"
}
