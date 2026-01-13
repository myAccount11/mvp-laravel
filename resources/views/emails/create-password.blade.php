<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Your Password</title>
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
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
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

        .email-header .icon-lock {
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

        /* Button */
        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .create-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4);
        }

        .create-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5);
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
            color: #7c3aed;
            word-break: break-all;
            font-size: 13px;
        }

        /* Security tips */
        .security-tips {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .security-tips h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .tip-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .tip-icon {
            width: 24px;
            height: 24px;
            background-color: #f3e8ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .tip-icon span {
            color: #7c3aed;
            font-size: 12px;
        }

        .tip-text {
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
            color: #7c3aed;
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

        /* Welcome box */
        .welcome-box {
            background: linear-gradient(135deg, #f3e8ff 0%, #fae8ff 100%);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e9d5ff;
        }

        .welcome-box p {
            color: #6b21a8;
            font-size: 15px;
            margin: 0;
            text-align: center;
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

            .create-button {
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
                <div class="icon-lock">🔐</div>
                <h1>Create Your Password</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p class="greeting">Hello {{ $name ?? 'User' }},</p>

                <div class="welcome-box">
                    <p>🎉 Your account has been created! Set up your password to get started.</p>
                </div>
                
                <p class="message">
                    An account has been created for you on Tourney App. To access your account and 
                    start managing tournaments, teams, and games, please create your password by 
                    clicking the button below.
                </p>

                <div class="button-container">
                    <a href="{{ $createPasswordUrl }}" class="create-button">
                        🔑 Create My Password
                    </a>
                </div>

                <div class="expiry-notice">
                    <p>
                        <span class="icon">⏰</span>
                        This link will expire in 48 hours. Please set up your password soon.
                    </p>
                </div>

                <div class="alt-link">
                    <p>If the button doesn't work, copy and paste this link into your browser:</p>
                    <a href="{{ $createPasswordUrl }}">{{ $createPasswordUrl }}</a>
                </div>

                <div class="security-tips">
                    <h3>🛡️ Password Security Tips:</h3>
                    <div class="tip-item">
                        <div class="tip-icon"><span>1</span></div>
                        <span class="tip-text">Use at least 8 characters with a mix of letters, numbers, and symbols</span>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon"><span>2</span></div>
                        <span class="tip-text">Avoid using personal information like your name or birthday</span>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon"><span>3</span></div>
                        <span class="tip-text">Don't reuse passwords from other websites</span>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon"><span>4</span></div>
                        <span class="tip-text">Consider using a password manager for better security</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>If you didn't request this, please contact our support team immediately.</p>
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

