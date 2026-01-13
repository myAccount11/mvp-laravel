<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Your Email</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
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

        .email-header .logo {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .email-header .logo span {
            font-size: 32px;
            color: #ffffff;
            font-weight: bold;
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

        /* Button */
        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
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
            color: #dc2626;
            word-break: break-all;
            font-size: 13px;
        }

        /* Features */
        .features {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .features h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            background-color: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .feature-icon span {
            color: #dc2626;
            font-size: 12px;
        }

        .feature-text {
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
            color: #dc2626;
        }

        /* Expiry notice */
        .expiry-notice {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 15px;
            margin-top: 25px;
        }

        .expiry-notice p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .expiry-notice .icon {
            margin-right: 10px;
            font-size: 18px;
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

            .verify-button {
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
                <div class="logo">
                    <span>M</span>
                </div>
                <h1>Verify Your Email</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p class="greeting">Hello {{ $name ?? 'User' }},</p>
                
                <p class="message">
                    Welcome to MVP App! We're excited to have you on board. To complete your registration 
                    and start using all the features, please verify your email address by clicking the 
                    button below.
                </p>

                <div class="button-container">
                    <a href="{{ $loginUrl }}" class="verify-button">
                        ✓ Verify My Email
                    </a>
                </div>

                <div class="expiry-notice">
                    <p>
                        <span class="icon">⏰</span>
                        This verification link will expire in 24 hours. Please verify your email soon.
                    </p>
                </div>

                <div class="alt-link">
                    <p>If the button doesn't work, copy and paste this link into your browser:</p>
                    <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
                </div>

                <div class="features">
                    <h3>What you can do after verification:</h3>
                    <div class="feature-item">
                        <div class="feature-icon"><span>🏆</span></div>
                        <span class="feature-text">Manage tournaments and competitions</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><span>👥</span></div>
                        <span class="feature-text">Create and manage teams</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><span>📊</span></div>
                        <span class="feature-text">Track live scores and statistics</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><span>📅</span></div>
                        <span class="feature-text">Schedule games and events</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>If you didn't create an account, you can safely ignore this email.</p>
                <p>© {{ date('Y') }} MVP App. All rights reserved.</p>
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

