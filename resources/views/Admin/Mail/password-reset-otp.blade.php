<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset OTP</title>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color: #004080;
            color: #ffffff;
            padding: 24px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
        }

        .content {
            padding: 24px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }

        .otp-box {
            margin: 20px 0;
            background-color: #f1f5f9;
            border: 1px dashed #004080;
            border-radius: 6px;
            text-align: center;
            padding: 20px;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 5px;
            color: #004080;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            background-color: #004080;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 4px;
        }

        .button:hover {
            background-color: #003366;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            {{ config('app.name') }} - Password Reset
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>We received a request to reset your password. Use the OTP below to complete your password reset process:
            </p>
            <div class="otp-box">
                {{ $otp }}
            </div>
            <p>This OTP is valid for <strong>{{ $expiration }} minutes</strong>. Do not share this code with anyone.
            </p>
            <p>
                <a href="{{ config('app.url') }}" class="button">Go to Application</a>
            </p>
            <p>If you did not request this, you can safely ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            This is an automated message. Please do not reply.
        </div>
    </div>
</body>

</html>
