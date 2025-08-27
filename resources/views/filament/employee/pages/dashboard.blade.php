<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-user class="w-8 h-8 text-primary-500" />
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Welcome, {{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee Portal</p>
                </div>
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="w-8 h-8 text-success-500" />
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Actions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your tasks</p>
                </div>
            </div>
        </x-filament::card>
        
        <x-filament::card>
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-text class="w-8 h-8 text-warning-500" />
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reports</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View your reports</p>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>