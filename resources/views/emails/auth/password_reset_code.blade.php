<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0056b3; /* Blue header */
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .verification-code {
            display: inline-block;
            background-color: #e6f0ff;
            color: #0056b3;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            padding: 15px 30px;
            border-radius: 6px;
            margin: 20px 0;
            border: 2px dashed #0056b3;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eeeeee;
        }
        p {
            margin-bottom: 20px;
        }
        .alert {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>You requested a password reset. Use the code below to reset your password:</p>
            
            <div class="verification-code">
                {{ $code }}
            </div>
            
            <p>This code will expire in 15 minutes.</p>
            
            <div class="alert">
                If you did not request a password reset, please ignore this email or contact support if you have concerns.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
