@php
    $data = $this->getViewData();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Quick Stats --}}
            <div class="lg:col-span-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Today's Overview</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $data['quickStats']['employees_joined_today'] }}</div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">New Employees</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $data['quickStats']['requests_today'] }}</div>
                        <div class="text-sm text-green-600 dark:text-green-400">Requests Today</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ $data['quickStats']['pending_requests'] }}</div>
                        <div class="text-sm text-orange-600 dark:text-orange-400">Pending</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $data['quickStats']['employees_on_vacation'] }}</div>
                        <div class="text-sm text-purple-600 dark:text-purple-400">On Vacation</div>
                    </div>
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $data['quickStats']['requests_this_month'] }}</div>
                        <div class="text-sm text-indigo-600 dark:text-indigo-400">This Month</div>
                    </div>
                    <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                            {{ $data['quickStats']['approval_rate'] }}%</div>
                        <div class="text-sm text-teal-600 dark:text-teal-400">Approval Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
