<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ get_setting('app_name', 'HRM System') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            padding: 20px;
            line-height: 1.6;
            color: #334155;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background: #10b981;
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        .welcome-badge {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 8px 16px;
            display: inline-block;
            color: white;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .welcome-title {
            color: white;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
            text-align: center;
        }

        .intro-text {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 32px;
            text-align: center;
            line-height: 1.6;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .card-title .icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            border-radius: 6px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: #64748b;
            font-size: 14px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .password-card {
            background: #8b5cf6;
            color: white;
        }

        .password-title {
            color: white !important;
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .password-title .icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .password-value {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 16px 20px;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 1px;
            text-align: center;
            margin: 12px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .password-note {
            font-size: 13px;
            opacity: 0.9;
            text-align: center;
            margin-top: 12px;
        }

        .cta-button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin: 32px auto;
            display: block;
            max-width: 240px;
            transition: background-color 0.2s ease;
        }

        .cta-button:hover {
            background: #2563eb;
        }

        .security-notice {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
        }

        .security-notice-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .security-notice-title::before {
            content: '⚠️';
            margin-right: 8px;
        }

        .security-notice-text {
            color: #92400e;
            font-size: 14px;
            line-height: 1.5;
        }

        .footer {
            background: #f1f5f9;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .contact-info {
            color: #475569;
            font-size: 13px;
        }

        .celebration {
            text-align: center;
            margin: 32px 0;
        }

        .celebration-emoji {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }

        .celebration-text {
            font-size: 18px;
            font-weight: 600;
            color: #3b82f6;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-container {
                border-radius: 15px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .welcome-title {
                font-size: 24px;
            }

            .greeting {
                font-size: 20px;
            }

            .card {
                padding: 20px;
            }

            .footer {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <div class="welcome-badge">🎉 New Employee</div>
            <h1 class="welcome-title">Welcome to {{ get_setting('app_name', 'HRM System') }}!</h1>
            <p class="welcome-subtitle">Your amazing journey starts here</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <h2 class="greeting">Hello {{ $employee->name }}! 👋</h2>
            <p class="intro-text">
                We're absolutely thrilled to have you join our team! Your account has been successfully created,
                and we can't wait to see all the incredible things you'll accomplish with us.
            </p>

            <!-- Employee Details Card -->
            <div class="card">
                <h3 class="card-title">
                    <span class="icon">👤</span>
                    Your Employee Profile
                </h3>
                <div class="info-row">
                    <span class="info-label">Employee ID</span>
                    <span class="info-value">{{ $employee->employee_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">{{ $employee->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Department</span>
                    <span class="info-value">{{ $employee->department?->name ?? 'To be assigned' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Position</span>
                    <span class="info-value">{{ $employee->position?->name ?? 'To be assigned' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Start Date</span>
                    <span
                        class="info-value">{{ $employee->company_date_of_joining?->format('F j, Y') ?? 'Today' }}</span>
                </div>
            </div>

            <!-- Password Card -->
            <div class="card password-card">
                <h3 class="password-title">
                    <span class="icon">🔑</span>
                    Your Login Credentials
                </h3>
                <div class="password-value">{{ $temporaryPassword }}</div>
                <div class="password-note">
                    ☝️ This is your temporary password. Please save it securely!
                </div>
            </div>

            <!-- CTA Button -->
            <a href="{{ $loginUrl }}" class="cta-button">
                🚀 Access Your Account
            </a>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="security-notice-title">Security First!</div>
                <div class="security-notice-text">
                    For your security, please change your password immediately after your first login.
                    Never share your login credentials with anyone, and always log out when using shared computers.
                </div>
            </div>

            <!-- Celebration Section -->
            <div class="celebration">
                <span class="celebration-emoji">🎊</span>
                <div class="celebration-text">Welcome to the Team!</div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="footer-text">
                Need help getting started? Our HR team is here to support you every step of the way.
            </div>
            <div class="contact-info">
                🏢 {{ get_setting('app_name', 'HRM System') }} - Human Resources Department
            </div>
        </div>
    </div>
</body>

</html>
