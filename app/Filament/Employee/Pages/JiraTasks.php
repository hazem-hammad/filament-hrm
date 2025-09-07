<?php

namespace App\Filament\Employee\Pages;

use App\Services\JiraServiceFactory;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class JiraTasks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.employee.pages.jira-tasks';

    protected static ?string $navigationLabel = 'JIRA Tasks';

    protected static ?string $title = 'My JIRA Tasks';

    protected static ?int $navigationSort = 2;

    // should register navigation
    protected static bool $shouldRegisterNavigation = true;

    public Collection $tasks;
    public array $filters = [];
    public ?string $selectedTask = null;
    public ?array $taskDetails = null;
    public bool $jiraConfigured = false;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->jiraConfigured = JiraServiceFactory::isConfigured();
        $this->tasks = collect();

        if ($this->jiraConfigured) {
            $this->loadTasks();
        } else {
            $this->errorMessage = 'JIRA integration is not configured. Please contact your administrator to set up JIRA credentials.';
        }

        $this->form->fill([
            'status_filter' => 'active',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\Select::make('status_filter')
                            ->label('Status Filter')
                            ->options([
                                'all' => 'All Tasks',
                                'active' => 'Active Tasks',
                                'todo' => 'To Do',
                                'in_progress' => 'In Progress',
                                'done' => 'Done',
                            ])
                            ->default('active')
                            ->live()
                            ->afterStateUpdated(fn() => $this->applyFilters()),

                        Forms\Components\Select::make('project_filter')
                            ->label('Project Filter')
                            ->options($this->getProjectOptions())
                            ->placeholder('All Projects')
                            ->live()
                            ->afterStateUpdated(fn() => $this->applyFilters()),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->compact(),
            ])
            ->statePath('filters');
    }

    public function loadTasks(): void
    {
        if (!$this->jiraConfigured) {
            return;
        }

        try {
            $jiraService = JiraServiceFactory::create();
            $user = $jiraService->getCurrentUser();

            $statusFilter = match ($this->filters['status_filter'] ?? 'active') {
                'active' => ['To Do', 'In Progress', 'Open', 'Reopened'],
                'todo' => ['To Do', 'Open'],
                'in_progress' => ['In Progress'],
                'done' => ['Done', 'Closed', 'Resolved'],
                default => []
            };

            $this->tasks = $jiraService->getAssignedTasks($user['accountId'], $statusFilter);

            // Apply project filter if set
            if (!empty($this->filters['project_filter'])) {
                $this->tasks = $this->tasks->filter(function ($task) {
                    return $task['project']['key'] === $this->filters['project_filter'];
                });
            }

            $this->errorMessage = null;
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to load JIRA tasks: ' . $e->getMessage();
            $this->tasks = collect();
        }
    }

    public function applyFilters(): void
    {
        $this->loadTasks();
    }

    public function refreshTasks(): void
    {
        $this->loadTasks();

        Notification::make()
            ->title('Tasks refreshed')
            ->success()
            ->send();
    }

    public function viewTaskDetails(string $taskKey): void
    {
        if (!$this->jiraConfigured) {
            return;
        }

        try {
            $jiraService = JiraServiceFactory::create();
            $this->taskDetails = $jiraService->getTaskById($taskKey);
            $this->selectedTask = $taskKey;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load task details')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function closeTaskDetails(): void
    {
        $this->selectedTask = null;
        $this->taskDetails = null;
    }

    public function openInJira(string $taskKey): void
    {
        $task = $this->tasks->firstWhere('key', $taskKey);
        if ($task && $task['url']) {
            $this->js('window.open("' . $task['url'] . '", "_blank")');
        }
    }

    private function getProjectOptions(): array
    {
        if (!$this->jiraConfigured) {
            return [];
        }

        $projects = [];
        foreach ($this->tasks as $task) {
            if (isset($task['project']['key'])) {
                $projects[$task['project']['key']] = $task['project']['name'] ?? $task['project']['key'];
            }
        }

        return $projects;
    }

    protected function getHeaderActions(): array
    {
        if (!$this->jiraConfigured) {
            return [];
        }

        return [
            Action::make('refresh')
                ->label('Refresh Tasks')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('refreshTasks'),
        ];
    }

    public function getTaskStatusColor(string $statusCategory): string
    {
        return match (strtolower($statusCategory)) {
            'new', 'indeterminate' => 'gray',
            'in progress' => 'warning',
            'done' => 'success',
            default => 'info'
        };
    }

    public function getTaskPriorityColor(string $priority): string
    {
        return match (strtolower($priority)) {
            'highest', 'blocker' => 'danger',
            'high', 'critical' => 'warning',
            'medium' => 'info',
            'low', 'minor' => 'gray',
            'lowest', 'trivial' => 'slate',
            default => 'gray'
        };
    }

    public static function canAccess(): bool
    {
        return Auth::guard('employee')->check();
    }
}
