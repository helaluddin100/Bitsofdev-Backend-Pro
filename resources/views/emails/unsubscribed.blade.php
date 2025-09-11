<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unsubscribed Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .container {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .sub-message {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Unsubscribed Successfully</h1>
        <div class="message">You have been unsubscribed from our marketing emails.</div>
        <div class="sub-message">
            We're sorry to see you go! You will no longer receive marketing emails from us.
        </div>
        <a href="{{ url('/') }}" class="btn">Return to Website</a>
    </div>
</body>
</html>
