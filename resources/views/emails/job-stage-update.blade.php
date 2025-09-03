<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            padding: 20px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: #667eea;
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .status-card {
            background: #f0f9ff;
            border-left: 4px solid #0891b2;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .status-label {
            font-size: 14px;
            font-weight: 600;
            color: #0891b2;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .status-value {
            font-size: 18px;
            font-weight: 700;
            color: #164e63;
        }

        .job-details {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }

        .job-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .job-info {
            font-size: 14px;
            color: #64748b;
        }

        .custom-content {
            margin: 25px 0;
            padding: 20px;
            background-color: #fefefe;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }

        .custom-content h2 {
            color: #1f2937;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .custom-content h3 {
            color: #374151;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .custom-content p {
            margin-bottom: 12px;
            line-height: 1.7;
        }

        .custom-content ul,
        .custom-content ol {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .custom-content li {
            margin-bottom: 5px;
        }

        .custom-content a {
            color: #2563eb;
            text-decoration: none;
        }

        .custom-content a:hover {
            text-decoration: underline;
        }

        .custom-content blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 16px;
            margin: 20px 0;
            font-style: italic;
            color: #6b7280;
        }

        .next-steps {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .next-steps-title {
            font-size: 16px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 10px;
        }

        .next-steps-content {
            color: #a16207;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .company-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 30px 0;
        }

        .timestamp {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            margin-top: 20px;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0 10px;
                border-radius: 8px;
            }

            .header,
            .content,
            .footer {
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Application Update</h1>
            <p>We have an important update regarding your application</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Job Details Card -->
            <div class="job-details">
                <div class="job-info">Position Applied For</div>
                <div class="job-title">{{ $jobApplication->job->title }}</div>
            </div>

            <!-- Status Update Card -->
            <div class="status-card">
                <div class="status-label">Current Stage</div>
                <div class="status-value">{{ $newStage->name }}</div>
            </div>

            @if ($customContent)
                <div class="custom-content">
                    {!! $customContent !!}
                </div>
            @else
                <p>Your application has been reviewed and moved to the next stage in our hiring process. We're impressed
                    with your qualifications and are excited to continue evaluating your candidacy.</p>

                <div class="next-steps">
                    <div class="next-steps-title">What's Next?</div>
                    <div class="next-steps-content">
                        Our hiring team will be in touch with you soon regarding the next steps in the process. Please
                        keep an eye on your email for further communications from us.
                    </div>
                </div>
            @endif

        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="company-name">{{ get_setting('app_name', 'HRM System') }}</div>
            <div class="footer-text">
                This is an automated message regarding your job application.<br>
                Please do not reply to this email.
            </div>
        </div>
    </div>
</body>

</html>
