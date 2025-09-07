<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JIRA Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for JIRA API integration
    |
    */

    'base_url' => env('JIRA_BASE_URL'),
    
    'email' => env('JIRA_EMAIL'),
    
    'api_token' => env('JIRA_API_TOKEN'),
    
    /*
    |--------------------------------------------------------------------------
    | Default Status Mappings
    |--------------------------------------------------------------------------
    |
    | Map JIRA status names to categories for filtering
    |
    */
    'status_mappings' => [
        'active' => ['To Do', 'In Progress', 'Open', 'Reopened', 'Selected for Development'],
        'todo' => ['To Do', 'Open', 'Backlog'],
        'in_progress' => ['In Progress', 'In Review', 'Testing'],
        'done' => ['Done', 'Closed', 'Resolved', 'Deployed'],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for JIRA API responses
    |
    */
    'cache' => [
        'enabled' => env('JIRA_CACHE_ENABLED', true),
        'ttl' => env('JIRA_CACHE_TTL', 300), // 5 minutes
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Request Configuration
    |--------------------------------------------------------------------------
    |
    | HTTP request settings
    |
    */
    'timeout' => env('JIRA_REQUEST_TIMEOUT', 30),
    
    'max_results' => env('JIRA_MAX_RESULTS', 50),
];