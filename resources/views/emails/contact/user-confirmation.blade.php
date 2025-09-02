<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting Bits Of Dev</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .highlight {
            color: #667eea;
            font-weight: bold;
        }
        .cta-button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thank You!</h1>
        <p>We've received your message and will get back to you soon</p>
    </div>
    
    <div class="content">
        <div class="message-box">
            <p>Dear <span class="highlight">{{ $contact->name }}</span>,</p>
            
            <p>Thank you for reaching out to <strong>Bits Of Dev</strong>! We've successfully received your message and our team will review it shortly.</p>
            
            <p>Here's a summary of what you sent us:</p>
            
            <div class="contact-info">
                <p><strong>Subject:</strong> {{ $contact->subject }}</p>
                <p><strong>Project Type:</strong> {{ ucwords(str_replace('-', ' ', $contact->project_type)) }}</p>
                @if($contact->company)
                <p><strong>Company:</strong> {{ $contact->company }}</p>
                @endif
                <p><strong>Message:</strong> {{ Str::limit($contact->message, 100) }}</p>
            </div>
            
            <p>We typically respond to all inquiries within <strong>24 hours</strong> during business days. If you have an urgent matter, please don't hesitate to call us directly.</p>
            
            <p>In the meantime, feel free to explore our website to learn more about our services and past projects.</p>
        </div>
        
        <a href="{{ config('app.url') }}" class="cta-button">Visit Our Website</a>
        
        <div class="footer">
            <p><strong>Bits Of Dev</strong></p>
            <p>Your trusted partner in web development and digital solutions</p>
            <p>Email: hello@bitsofdev.com | Phone: +1 (555) 123-4567</p>
        </div>
    </div>
</body>
</html>
