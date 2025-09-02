<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application Received</title>
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
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e97176;
        }
        .header h1 {
            color: #e97176;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 20px;
        }
        .applicant-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #e97176;
        }
        .info-row {
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
        }
        .info-label {
            font-weight: bold;
            min-width: 140px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .job-info {
            background-color: #e8f4f8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #e97176;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 New Job Application Received!</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>A new job application has been submitted for the following position:</p>
            
            <div class="job-info">
                <h3 style="margin-top: 0; color: #17a2b8;">{{ $jobApplication->job->title }}</h3>
                <div class="info-row">
                    <span class="info-label">Application ID:</span>
                    <span class="info-value">#{{ $jobApplication->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submitted Date:</span>
                    <span class="info-value">{{ $jobApplication->created_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
            </div>
            
            <div class="applicant-info">
                <h3 style="margin-top: 0; color: #e97176;">👤 Applicant Information</h3>
                
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $jobApplication->full_name }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">
                        <a href="mailto:{{ $jobApplication->email }}">{{ $jobApplication->email }}</a>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">
                        <a href="tel:{{ $jobApplication->phone }}">{{ $jobApplication->phone }}</a>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Experience:</span>
                    <span class="info-value">{{ $jobApplication->years_of_experience }} years</span>
                </div>
                
                @if($jobApplication->linkedin_url)
                <div class="info-row">
                    <span class="info-label">LinkedIn:</span>
                    <span class="info-value">
                        <a href="{{ $jobApplication->linkedin_url }}" target="_blank">View Profile</a>
                    </span>
                </div>
                @endif
                
                @if($jobApplication->getFirstMedia('resume'))
                <div class="info-row">
                    <span class="info-label">Resume:</span>
                    <span class="info-value">
                        <a href="{{ $jobApplication->getFirstMedia('resume')->getUrl() }}" target="_blank">View Resume</a>
                    </span>
                </div>
                @endif
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <p>Please review the application in your admin dashboard.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from your job application system.</p>
            <p style="margin: 0;">© {{ date('Y') }} {{ get_setting('company_name', 'Company') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>