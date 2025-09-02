<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
        .contact-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }
        .field-value {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: #667eea;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
        <p>You have received a new message from your website</p>
    </div>
    
    <div class="content">
        <div class="contact-details">
            <div class="field">
                <span class="field-label">Name:</span>
                <span class="field-value">{{ $contact->name }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Email:</span>
                <span class="field-value">{{ $contact->email }}</span>
            </div>
            
            @if($contact->company)
            <div class="field">
                <span class="field-label">Company:</span>
                <span class="field-value">{{ $contact->company }}</span>
            </div>
            @endif
            
            <div class="field">
                <span class="field-label">Subject:</span>
                <span class="field-value">{{ $contact->subject }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Project Type:</span>
                <span class="field-value">{{ ucwords(str_replace('-', ' ', $contact->project_type)) }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Message:</span>
                <div class="field-value" style="white-space: pre-wrap;">{{ $contact->message }}</div>
            </div>
            
            <div class="field">
                <span class="field-label">Submitted:</span>
                <span class="field-value">{{ $contact->created_at->format('F j, Y \a\t g:i A') }}</span>
            </div>
        </div>
        
        <a href="{{ config('app.url') }}/admin/contacts" class="cta-button">View in Dashboard</a>
        
        <div class="footer">
            <p>This is an automated notification from your Bits Of Dev website.</p>
            <p>Please respond to this inquiry as soon as possible.</p>
        </div>
    </div>
</body>
</html>
