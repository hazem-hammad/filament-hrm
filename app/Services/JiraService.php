<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

final class JiraService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $email,
        private readonly string $apiToken
    ) {}

    public function getAssignedTasks(string $assigneeAccountId, array $statuses = []): Collection
    {
        $jql = "assignee = '{$assigneeAccountId}'";
        
        if (!empty($statuses)) {
            $statusList = "'" . implode("','", $statuses) . "'";
            $jql .= " AND status IN ({$statusList})";
        }
        
        $jql .= " ORDER BY updated DESC";

        $response = $this->makeRequest('GET', '/rest/api/3/search', [
            'jql' => $jql,
            'maxResults' => 50,
            'fields' => [
                'key',
                'summary',
                'description', 
                'status',
                'priority',
                'issuetype',
                'assignee',
                'reporter',
                'created',
                'updated',
                'duedate',
                'project'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch JIRA tasks: ' . $response->body());
        }

        return collect($response->json('issues', []))->map(function ($issue) {
            return $this->transformIssue($issue);
        });
    }

    public function getTaskById(string $issueKey): array
    {
        $response = $this->makeRequest('GET', "/rest/api/3/issue/{$issueKey}", [
            'fields' => [
                'key',
                'summary',
                'description',
                'status',
                'priority',
                'issuetype',
                'assignee',
                'reporter',
                'created',
                'updated',
                'duedate',
                'project',
                'worklog'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch JIRA task: ' . $response->body());
        }

        return $this->transformIssue($response->json());
    }

    public function getCurrentUser(): array
    {
        $response = $this->makeRequest('GET', '/rest/api/3/myself');

        if ($response->failed()) {
            throw new \Exception('Failed to fetch current user: ' . $response->body());
        }

        return $response->json();
    }

    public function getProjects(): Collection
    {
        $response = $this->makeRequest('GET', '/rest/api/3/project/search', [
            'maxResults' => 100
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch projects: ' . $response->body());
        }

        return collect($response->json('values', []))->map(function ($project) {
            return [
                'key' => $project['key'],
                'name' => $project['name'],
                'id' => $project['id'],
                'projectTypeKey' => $project['projectTypeKey'] ?? null,
                'avatarUrls' => $project['avatarUrls'] ?? null,
            ];
        });
    }

    private function makeRequest(string $method, string $endpoint, array $params = []): Response
    {
        $url = rtrim($this->baseUrl, '/') . $endpoint;
        
        $request = Http::withBasicAuth($this->email, $this->apiToken)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        return match (strtoupper($method)) {
            'GET' => $request->get($url, $params),
            'POST' => $request->post($url, $params),
            'PUT' => $request->put($url, $params),
            'DELETE' => $request->delete($url, $params),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}")
        };
    }

    private function transformIssue(array $issue): array
    {
        $fields = $issue['fields'] ?? [];

        return [
            'key' => $issue['key'] ?? null,
            'id' => $issue['id'] ?? null,
            'summary' => $fields['summary'] ?? null,
            'description' => $fields['description'] ?? null,
            'status' => [
                'name' => $fields['status']['name'] ?? null,
                'statusCategory' => $fields['status']['statusCategory']['name'] ?? null,
            ],
            'priority' => [
                'name' => $fields['priority']['name'] ?? null,
                'iconUrl' => $fields['priority']['iconUrl'] ?? null,
            ],
            'issuetype' => [
                'name' => $fields['issuetype']['name'] ?? null,
                'iconUrl' => $fields['issuetype']['iconUrl'] ?? null,
            ],
            'assignee' => [
                'accountId' => $fields['assignee']['accountId'] ?? null,
                'displayName' => $fields['assignee']['displayName'] ?? null,
                'emailAddress' => $fields['assignee']['emailAddress'] ?? null,
                'avatarUrls' => $fields['assignee']['avatarUrls'] ?? null,
            ],
            'reporter' => [
                'accountId' => $fields['reporter']['accountId'] ?? null,
                'displayName' => $fields['reporter']['displayName'] ?? null,
                'emailAddress' => $fields['reporter']['emailAddress'] ?? null,
                'avatarUrls' => $fields['reporter']['avatarUrls'] ?? null,
            ],
            'project' => [
                'key' => $fields['project']['key'] ?? null,
                'name' => $fields['project']['name'] ?? null,
                'avatarUrls' => $fields['project']['avatarUrls'] ?? null,
            ],
            'created' => $fields['created'] ?? null,
            'updated' => $fields['updated'] ?? null,
            'duedate' => $fields['duedate'] ?? null,
            'worklog' => $fields['worklog'] ?? null,
            'url' => $this->baseUrl . '/browse/' . ($issue['key'] ?? ''),
        ];
    }
}