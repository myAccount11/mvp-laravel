<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Your Password</title>
    <style>
        /* Reset styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f4f7fa;
        }

        /* Container */
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .email-header .icon-key {
            font-size: 48px;
            margin-bottom: 10px;
        }

        /* Body */
        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            color: #4b5563;
            font-size: 16px;
            margin-bottom: 30px;
        }

        /* Alert box */
        .alert-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #fcd34d;
        }

        .alert-box p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
            display: flex;
            align-items: flex-start;
        }

        .alert-box .icon {
            margin-right: 10px;
            font-size: 20px;
            flex-shrink: 0;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
        }

        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
        }

        /* Alternative link */
        .alt-link {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .alt-link p {
            color: #6b7280;
            font-size: 14px;
            margin: 0 0 10px 0;
        }

        .alt-link a {
            color: #0ea5e9;
            word-break: break-all;
            font-size: 13px;
        }

        /* Security notice */
        .security-notice {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .security-notice h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .notice-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .notice-icon {
            width: 24px;
            height: 24px;
            background-color: #e0f2fe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .notice-icon span {
            color: #0ea5e9;
            font-size: 12px;
        }

        .notice-text {
            color: #4b5563;
            font-size: 14px;
        }

        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #9ca3af;
            font-size: 13px;
            margin: 0 0 10px 0;
        }

        .email-footer .social-links {
            margin-top: 15px;
        }

        .email-footer .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
            font-size: 13px;
        }

        .email-footer .social-links a:hover {
            color: #0ea5e9;
        }

        /* Expiry notice */
        .expiry-notice {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin-top: 25px;
        }

        .expiry-notice p {
            color: #991b1b;
            font-size: 14px;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .expiry-notice .icon {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Didn't request box */
        .didnt-request {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }

        .didnt-request h4 {
            color: #166534;
            font-size: 15px;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
        }

        .didnt-request h4 .icon {
            margin-right: 8px;
        }

        .didnt-request p {
            color: #15803d;
            font-size: 14px;
            margin: 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .reset-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="icon-key">🔑</div>
                <h1>Reset Your Password</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p class="greeting">Hello {{ $name ?? 'User' }},</p>

                <div class="alert-box">
                    <p>
                        <span class="icon">⚠️</span>
                        We received a request to reset the password associated with your account. 
                        If you made this request, click the button below to proceed.
                    </p>
                </div>
                
                <p class="message">
                    To reset your password, please click the button below. You'll be redirected to 
                    a secure page where you can create a new password for your Tourney App account.
                </p>

                <div class="button-container">
                    <a href="{{ $resetPasswordUrl }}" class="reset-button">
                        🔄 Reset My Password
                    </a>
                </div>

                <div class="expiry-notice">
                    <p>
                        <span class="icon">⏰</span>
                        This link will expire in 1 hour for security reasons.
                    </p>
                </div>

                <div class="alt-link">
                    <p>If the button doesn't work, copy and paste this link into your browser:</p>
                    <a href="{{ $resetPasswordUrl }}">{{ $resetPasswordUrl }}</a>
                </div>

                <div class="didnt-request">
                    <h4><span class="icon">✅</span> Didn't request this?</h4>
                    <p>
                        If you didn't request a password reset, you can safely ignore this email. 
                        Your password will remain unchanged and your account is secure.
                    </p>
                </div>

                <div class="security-notice">
                    <h3>🛡️ Security Reminders:</h3>
                    <div class="notice-item">
                        <div class="notice-icon"><span>🔒</span></div>
                        <span class="notice-text">Never share your password with anyone</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-icon"><span>📧</span></div>
                        <span class="notice-text">We will never ask for your password via email</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-icon"><span>🔗</span></div>
                        <span class="notice-text">Always check the URL before entering your credentials</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-icon"><span>📱</span></div>
                        <span class="notice-text">Enable two-factor authentication for extra security</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>If you're having trouble, contact our support team for assistance.</p>
                <p>© {{ date('Y') }} Tourney App. All rights reserved.</p>
                <div class="social-links">
                    <a href="#">Help Center</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

