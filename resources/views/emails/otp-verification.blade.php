<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Kape Na!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            max-width: 300px;
            height: auto;
        }
        .content {
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .otp-container {
            background-color: #f8f9fa;
            border: 2px dashed #d3ad7f;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #d3ad7f;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .expiry-notice {
            font-size: 14px;
            color: #999;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .warning {
            font-size: 14px;
            color: #999;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-container">
        @if($logoDataUri)
        <div class="logo-container">
            <img src="{{ $logoDataUri }}" alt="Kape Na! Logo" style="max-width: 300px; height: auto;" />
        </div>
        @else
        <div class="logo-container">
            <h1 style="color: #d3ad7f; font-size: 32px; margin: 0;">KAPE NA!</h1>
            <p style="color: #666; font-size: 14px; margin-top: 5px;">Your Premium Coffee Experience</p>
        </div>
        @endif
        
        <div class="content">
            <div class="greeting">
                Hello {{ $user->admin_name ?? $user->name }}!
            </div>
            
            <div class="message">
                Thank you for registering with <strong>Kape Na!</strong>
            </div>
            
            <div class="message">
                Please use the following 6-digit code to verify your {{ isset($user->admin_name) ? 'admin ' : '' }}email address:
            </div>
            
            <div class="otp-container">
                <div class="otp-label">Verification Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
            </div>
            
            <div class="expiry-notice">
                ⏰ This code will expire in 10 minutes.
            </div>
            
            <div class="warning">
                If you did not create an account, please ignore this email.
            </div>
        </div>
        
        <div class="footer">
            <p>Best regards,<br><strong>Kape Na! Team</strong></p>
            <p style="margin-top: 10px; color: #d3ad7f;">Your Premium Coffee Experience</p>
        </div>
    </div>
</body>
</html>

