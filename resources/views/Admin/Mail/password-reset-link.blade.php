<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset - {{ $app }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 40px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #1d4ed8;
            color: #ffffff;
            text-align: center;
            padding: 24px;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 24px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            padding: 14px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            padding: 20px;
        }

        .footer img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        .break-all {
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">{{ $app }}</div>
        <div class="content">
            <p>Hi {{ $name }},</p>
            <p>We received a request to reset the password for your account ({{ $email }}).</p>
            <p>To reset your password, click the button below:</p>
            <p style="text-align: center;">
                <a href="{{ $url }}" class="btn">Reset Password</a>
            </p>
            <p>If the button above doesn’t work, copy and paste the following URL into your browser:</p>
            <p class="break-all">{{ $url }}</p>
            <p>If you did not request a password reset, no further action is required.</p>
            <p>Regards,<br><strong>{{ $app }} Team</strong></p>
        </div>
        <div class="footer">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $app }}">
            <p>© {{ date('Y') }} {{ $app }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
