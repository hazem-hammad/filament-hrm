<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-blue-800 dark:text-blue-200">My Balance Overview</h2>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Current year: {{ $currentYear }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Track your remaining vacation days and attendance
                allowances for the current year.</p>
        </div>

        <!-- Vacation Types Section -->
        @if ($vacationBalances->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🏖️</span>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Vacation Balance</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($vacationBalances as $balance)
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $balance['type']->name }}
                                </h4>
                                <span class="text-2xl">{{ $balance['type']->icon ?? '📅' }}</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Remaining Days:</span>
                                    <span
                                        class="font-bold text-green-600 dark:text-green-400">{{ $balance['remaining'] }}
                                        / {{ $balance['total'] }}</span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500"
                                        style="width: {{ $balance['percentage'] }}%"></div>
                                </div>

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>Used: {{ $balance['used'] }} days</span>
                                    <span>{{ $balance['percentage'] }}% remaining</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Attendance Types Section -->
        @if ($attendanceBalances->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⏰</span>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Attendance Balance</h3>
                    </div>
                    <span
                        class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                        Current Month: {{ $currentMonth }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($attendanceBalances as $balance)
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $balance['type']->name }}
                                </h4>
                                <span class="text-2xl">{{ $balance['type']->icon ?? '🕒' }}</span>
                            </div>

                            <div class="space-y-3">
                                @if ($balance['hasLimit'])
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            Remaining {{ ucfirst($balance['unit']) }}:
                                        </span>
                                        <span class="font-bold text-blue-600 dark:text-blue-400">
                                            {{ $balance['remaining'] }} / {{ $balance['total'] }}
                                        </span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500"
                                            style="width: {{ $balance['percentage'] }}%"></div>
                                    </div>

                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>Used: {{ $balance['used'] }} {{ $balance['unit'] }}</span>
                                        <span>{{ $balance['percentage'] }}% remaining</span>
                                    </div>

                                    @if ($balance['unit'] === 'requests' && $balance['usedHours'] > 0)
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400 pt-1 border-t border-gray-100 dark:border-gray-600">
                                            Total hours used: {{ $balance['usedHours'] }}
                                            @if ($balance['maxHoursPerMonth'])
                                                / {{ $balance['maxHoursPerMonth'] }} max
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Usage:</span>
                                        <span
                                            class="font-bold text-green-600 dark:text-green-400">{{ $balance['remaining'] }}</span>
                                    </div>

                                    <!-- Unlimited indicator -->
                                    <div class="w-full bg-green-100 dark:bg-green-900/30 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full"
                                            style="width: 100%"></div>
                                    </div>

                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>Used: {{ $balance['usedRequests'] }} requests</span>
                                        <span>No limits applied</span>
                                    </div>

                                    @if ($balance['usedHours'] > 0)
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400 pt-1 border-t border-gray-100 dark:border-gray-600">
                                            Total hours used: {{ $balance['usedHours'] }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Empty State -->
        @if ($vacationBalances->count() == 0 && $attendanceBalances->count() == 0)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
                <div
                    class="mx-auto w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No Balance Information Available
                </h3>
                <p class="text-gray-600 dark:text-gray-400">No vacation types or attendance types have been configured
                    yet.</p>
            </div>
        @endif

    </div>
</x-filament-panels::page>
