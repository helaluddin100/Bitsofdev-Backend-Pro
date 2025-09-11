<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $campaign->email_subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
        .unsubscribe {
            margin-top: 10px;
        }
        .unsubscribe a {
            color: #6c757d;
            text-decoration: none;
        }
        .tracking-pixel {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $campaign->name }}</h1>
        @if($campaign->description)
            <p>{{ $campaign->description }}</p>
        @endif
    </div>

    <div class="content">
        <h2>Hello {{ $lead->name }},</h2>

        {!! $campaign->email_body !!}

        <p>Best regards,<br>
        {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $lead->email }} regarding {{ $lead->category }} services.</p>

        <div class="unsubscribe">
            <a href="{{ $unsubscribeUrl }}">Unsubscribe from future emails</a>
        </div>

        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>

    <!-- Tracking Pixel -->
    <img src="{{ $trackingPixel }}" alt="" class="tracking-pixel" width="1" height="1">
</body>
</html>
