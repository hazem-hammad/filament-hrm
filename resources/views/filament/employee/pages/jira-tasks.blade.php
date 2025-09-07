<x-filament-panels::page>
    @if($this->jiraConfigured)
        <div class="space-y-6">
            <!-- Filters -->
            <x-filament::section>
                <x-slot name="heading">
                    Filters
                </x-slot>
                {{ $this->form }}
            </x-filament::section>

            <!-- Tasks List -->
            @if($this->errorMessage)
                <x-filament::section>
                    <div class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Error Loading Tasks
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    {{ $this->errorMessage }}
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @elseif($tasks->isNotEmpty())
                <x-filament::section>
                    <x-slot name="heading">
                        My Tasks ({{ $tasks->count() }})
                    </x-slot>
                    
                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $task['key'] }}
                                            </h3>
                                            <x-filament::badge 
                                                :color="$this->getTaskStatusColor($task['status']['statusCategory'])"
                                                size="sm">
                                                {{ $task['status']['name'] }}
                                            </x-filament::badge>
                                            <x-filament::badge 
                                                :color="$this->getTaskPriorityColor($task['priority']['name'] ?? '')"
                                                size="sm"
                                                class="opacity-80">
                                                {{ $task['priority']['name'] ?? 'No Priority' }}
                                            </x-filament::badge>
                                        </div>
                                        
                                        <p class="mt-2 text-sm text-gray-900 font-medium dark:text-gray-100">
                                            {{ $task['summary'] }}
                                        </p>
                                        
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <x-heroicon-s-folder class="mr-1 h-3 w-3" />
                                                {{ $task['project']['name'] ?? $task['project']['key'] }}
                                            </span>
                                            <span class="flex items-center">
                                                <x-heroicon-s-tag class="mr-1 h-3 w-3" />
                                                {{ $task['issuetype']['name'] ?? 'Unknown' }}
                                            </span>
                                            @if($task['updated'])
                                                <span class="flex items-center">
                                                    <x-heroicon-s-clock class="mr-1 h-3 w-3" />
                                                    Updated: {{ \Carbon\Carbon::parse($task['updated'])->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <x-filament::button
                                            wire:click="viewTaskDetails('{{ $task['key'] }}')"
                                            size="sm"
                                            color="primary">
                                            View Details
                                        </x-filament::button>
                                        
                                        <x-filament::button
                                            wire:click="openInJira('{{ $task['key'] }}')"
                                            size="sm"
                                            color="gray"
                                            icon="heroicon-o-arrow-top-right-on-square">
                                            Open in JIRA
                                        </x-filament::button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="text-center py-12">
                        <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tasks found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No JIRA tasks match your current filters.
                        </p>
                    </div>
                </x-filament::section>
            @endif
        </div>

        <!-- Task Details Modal -->
        @if($selectedTask && $taskDetails)
            <div
                x-data="{ open: @entangle('selectedTask').live }"
                x-show="open"
                x-on:keydown.escape.window="$wire.closeTaskDetails()"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
            >
                <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div
                        x-show="open"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
                        x-on:click="$wire.closeTaskDetails()"
                    ></div>

                    <!-- Modal panel -->
                    <div
                        x-show="open"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block w-full max-w-4xl transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:align-middle"
                    >
                        <!-- Modal header -->
                        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Task Details - {{ $taskDetails['key'] }}
                                </h3>
                                <button
                                    wire:click="closeTaskDetails"
                                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                >
                                    <x-heroicon-o-x-mark class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Modal body -->
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $taskDetails['summary'] }}
                                </h3>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <x-filament::badge 
                                        :color="$this->getTaskStatusColor($taskDetails['status']['statusCategory'])"
                                        size="md">
                                        {{ $taskDetails['status']['name'] }}
                                    </x-filament::badge>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <x-filament::badge 
                                        :color="$this->getTaskPriorityColor($taskDetails['priority']['name'] ?? '')"
                                        size="md">
                                        {{ $taskDetails['priority']['name'] ?? 'No Priority' }}
                                    </x-filament::badge>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $taskDetails['project']['name'] ?? $taskDetails['project']['key'] }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Type</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $taskDetails['issuetype']['name'] ?? 'Unknown' }}
                                    </p>
                                </div>
                                
                                @if($taskDetails['assignee']['displayName'])
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assignee</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $taskDetails['assignee']['displayName'] }}
                                        </p>
                                    </div>
                                @endif
                                
                                @if($taskDetails['reporter']['displayName'])
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reporter</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $taskDetails['reporter']['displayName'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($taskDetails['description'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                    <div class="rounded-md bg-gray-50 p-3 dark:bg-gray-800">
                                        <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                            {{ is_string($taskDetails['description']) ? $taskDetails['description'] : 'No description available' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                                @if($taskDetails['created'])
                                    <div>
                                        <label class="block font-medium">Created</label>
                                        <p>{{ \Carbon\Carbon::parse($taskDetails['created'])->format('M j, Y g:i A') }}</p>
                                    </div>
                                @endif
                                
                                @if($taskDetails['updated'])
                                    <div>
                                        <label class="block font-medium">Updated</label>
                                        <p>{{ \Carbon\Carbon::parse($taskDetails['updated'])->format('M j, Y g:i A') }}</p>
                                    </div>
                                @endif
                                
                                @if($taskDetails['duedate'])
                                    <div>
                                        <label class="block font-medium">Due Date</label>
                                        <p>{{ \Carbon\Carbon::parse($taskDetails['duedate'])->format('M j, Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                            <div class="flex justify-end space-x-3">
                                <x-filament::button
                                    wire:click="openInJira('{{ $taskDetails['key'] }}')"
                                    color="primary"
                                    icon="heroicon-o-arrow-top-right-on-square">
                                    Open in JIRA
                                </x-filament::button>
                                
                                <x-filament::button
                                    wire:click="closeTaskDetails"
                                    color="gray">
                                    Close
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- JIRA Not Configured -->
        <x-filament::section>
            <div class="rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            JIRA Integration Not Configured
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>JIRA integration is not set up. Please contact your administrator to configure JIRA credentials.</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>