<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Invitation</title>
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
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name') }}</h1>
    </div>

    <div class="content">
        <h2>You're Invited!</h2>

        <p>Hello,</p>

        <p>You have been invited to join <strong>{{ config('app.name') }}</strong> as a
            <strong>{{ $invitation->role->name }}</strong>.
        </p>

        <p>To accept this invitation and create your account, please click the button below:</p>

        <div style="text-align: center;">
            <a href="{{ $invitationLink }}" class="button">Accept Invitation</a>
        </div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>This invitation will expire on {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}</li>
            <li>If you don't want to accept this invitation, you can simply ignore this email</li>
            <li>If the button doesn't work, you can copy and paste this link into your browser:</li>
        </ul>

        <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">
            {{ $invitationLink }}
        </p>

        <p>If you have any questions, please contact the administrator.</p>

        <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>This invitation was sent by {{ $invitation->inviter->name ?? 'an administrator' }}.</p>
        <p>If you didn't expect this invitation, please ignore this email.</p>
    </div>
</body>

</html>