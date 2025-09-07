<?php

namespace App\Services;

final class JiraServiceFactory
{
    public static function create(): JiraService
    {
        $baseUrl = config('jira.base_url');
        $email = config('jira.email');
        $apiToken = config('jira.api_token');

        if (empty($baseUrl) || empty($email) || empty($apiToken)) {
            throw new \Exception('JIRA configuration is incomplete. Please check your JIRA settings.');
        }

        return new JiraService($baseUrl, $email, $apiToken);
    }

    public static function isConfigured(): bool
    {
        return !empty(config('jira.base_url')) && 
               !empty(config('jira.email')) && 
               !empty(config('jira.api_token'));
    }
}