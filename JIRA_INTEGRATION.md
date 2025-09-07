# JIRA Integration Documentation

## Overview

The JIRA integration allows employees to view their assigned JIRA tasks directly from the employee dashboard. This integration uses the JIRA REST API v3 to retrieve and display task information.

## Features

- View assigned JIRA tasks with filtering options
- Filter tasks by status (All, Active, To Do, In Progress, Done)
- Filter tasks by project
- View detailed task information in a modal
- Direct links to open tasks in JIRA
- Real-time task refresh
- Responsive design for mobile and desktop

## Setup Instructions

### 1. JIRA API Token

First, you need to create a JIRA API token:

1. Log in to your JIRA instance
2. Go to Account settings → Security → Create and manage API tokens
3. Click "Create API token"
4. Give it a name (e.g., "HRM Integration")
5. Copy the generated token

### 2. Environment Configuration

Add the following variables to your `.env` file:

```env
# JIRA Integration Configuration
JIRA_BASE_URL=https://your-domain.atlassian.net
JIRA_EMAIL=your-email@company.com
JIRA_API_TOKEN=your_jira_api_token
JIRA_CACHE_ENABLED=true
JIRA_CACHE_TTL=300
JIRA_REQUEST_TIMEOUT=30
JIRA_MAX_RESULTS=50
```

Replace:
- `https://your-domain.atlassian.net` with your actual JIRA instance URL
- `your-email@company.com` with the email address used for JIRA authentication
- `your_jira_api_token` with the API token created in step 1

### 3. Cache Configuration (Optional)

Run the following commands to clear and rebuild the configuration cache:

```bash
php artisan config:cache
php artisan cache:clear
```

## Usage

### For Employees

1. Log in to the employee dashboard
2. Navigate to "JIRA Tasks" from the main menu
3. Use filters to narrow down tasks:
   - **Status Filter**: Filter by task status (Active, To Do, In Progress, Done)
   - **Project Filter**: Filter by specific JIRA projects
4. Click "View Details" to see complete task information
5. Click "Open in JIRA" to navigate to the task in JIRA
6. Use "Refresh Tasks" to get the latest data

### Task Information Displayed

- Task Key (e.g., PROJ-123)
- Summary/Title
- Current Status with color coding
- Priority with color coding
- Project information
- Issue type
- Assignee and Reporter
- Creation and update dates
- Due date (if set)
- Description (in detail view)

## API Endpoints Used

The integration uses the following JIRA REST API endpoints:

- `GET /rest/api/3/search` - Search for issues using JQL
- `GET /rest/api/3/issue/{issueKey}` - Get detailed issue information
- `GET /rest/api/3/myself` - Get current user information
- `GET /rest/api/3/project/search` - Get project information

## Security Considerations

- API tokens are stored in environment variables and not in the database
- All API calls use HTTPS for secure communication
- The integration only reads data from JIRA (no write operations)
- Only assigned tasks are visible to each employee

## Troubleshooting

### Common Issues

1. **"JIRA integration is not configured"**
   - Ensure all environment variables are set correctly
   - Run `php artisan config:cache` after updating environment variables

2. **"Failed to load JIRA tasks"**
   - Check your JIRA base URL format
   - Verify your API token is valid
   - Ensure the email address matches your JIRA account
   - Check network connectivity to JIRA instance

3. **Empty task list**
   - Verify that tasks are actually assigned to the user
   - Check if the email in configuration matches the JIRA user email
   - Try different status filters

### Testing the Configuration

You can test the JIRA connection by running:

```bash
php artisan tinker
```

Then execute:

```php
$service = \App\Services\JiraServiceFactory::create();
$user = $service->getCurrentUser();
dd($user);
```

This should return your JIRA user information if the configuration is correct.

## Performance Optimization

- Tasks are cached for 5 minutes by default (configurable via `JIRA_CACHE_TTL`)
- Maximum 50 results per request (configurable via `JIRA_MAX_RESULTS`)
- Pagination is handled automatically for large result sets

## Status Color Coding

- **Gray**: New/Indeterminate status
- **Blue**: General information status
- **Yellow**: In Progress status
- **Green**: Done/Completed status

## Priority Color Coding

- **Red**: Highest/Blocker priority
- **Yellow**: High/Critical priority
- **Blue**: Medium priority
- **Gray**: Low/Minor priority
- **Slate**: Lowest/Trivial priority