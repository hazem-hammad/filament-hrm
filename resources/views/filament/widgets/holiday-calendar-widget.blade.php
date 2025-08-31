<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-heroicon-o-calendar-days class="w-5 h-5 text-blue-500" />
                <span>Holiday Calendar - {{ $this->getViewData()['currentMonth'] }}</span>
                @if($this->getViewData()['totalHolidaysThisYear'] > 0)
                    <x-filament::badge color="info" size="sm">
                        {{ $this->getViewData()['totalHolidaysThisYear'] }} holidays this year
                    </x-filament::badge>
                @endif
            </div>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar Grid -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Calendar Header -->
                    <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="p-2 text-center text-xs font-medium text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-600 last:border-r-0">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Calendar Body -->
                    <div class="grid grid-cols-7">
                        @foreach($this->getViewData()['calendarDays'] as $dayData)
                            <div class="min-h-[80px] border-r border-b border-gray-200 dark:border-gray-600 last:border-r-0 p-1
                                {{ !$dayData['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-700' }}
                                {{ $dayData['isToday'] ? 'ring-2 ring-blue-500 ring-inset' : '' }}">
                                
                                <div class="flex flex-col h-full">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-sm font-medium 
                                            {{ !$dayData['isCurrentMonth'] ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100' }}
                                            {{ $dayData['isToday'] ? 'text-blue-600 dark:text-blue-400 font-bold' : '' }}">
                                            {{ $dayData['day'] }}
                                        </span>
                                    </div>

                                    @if($dayData['holidays']->count() > 0)
                                        <div class="flex-1 space-y-0.5">
                                            @foreach($dayData['holidays']->take(2) as $holiday)
                                                <div class="text-xs px-1 py-0.5 rounded truncate text-white font-medium"
                                                     style="background-color: {{ $holiday->color }}">
                                                    {{ $holiday->name }}
                                                </div>
                                            @endforeach
                                            
                                            @if($dayData['holidays']->count() > 2)
                                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                                                    +{{ $dayData['holidays']->count() - 2 }} more
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Current Holidays -->
                @if($this->getViewData()['currentHolidays']->count() > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <x-heroicon-s-sparkles class="w-4 h-4 text-green-500" />
                            Current Holidays
                        </h3>
                        <div class="space-y-3">
                            @foreach($this->getViewData()['currentHolidays'] as $holiday)
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                    <div class="flex items-start gap-2">
                                        <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1" style="background-color: {{ $holiday->color }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $holiday->name }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $holiday->formatted_date_range }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    {{ ucfirst($holiday->type) }}
                                                </span>
                                                @if($holiday->is_paid)
                                                    <span class="text-xs text-green-600 dark:text-green-400">💰 Paid</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upcoming Holidays -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4 text-blue-500" />
                        Upcoming Holidays (Next 90 days)
                    </h3>
                    
                    @if($this->getViewData()['upcomingHolidays']->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($this->getViewData()['upcomingHolidays'] as $holiday)
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-start gap-2">
                                        <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1" style="background-color: {{ $holiday->color }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $holiday->name }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $holiday->formatted_date_range }}
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                                                {{ now()->diffInDays($holiday->start_date) }} days away
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                    {{ $holiday->type === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                                                    {{ $holiday->type === 'religious' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
                                                    {{ $holiday->type === 'national' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
                                                    {{ $holiday->type === 'company' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}">
                                                    {{ ucfirst($holiday->type) }}
                                                </span>
                                                @if($holiday->is_paid)
                                                    <span class="text-xs text-green-600 dark:text-green-400">💰 Paid</span>
                                                @endif
                                                @if($holiday->is_recurring)
                                                    <span class="text-xs text-blue-600 dark:text-blue-400">🔄 Recurring</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-calendar-x class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No upcoming holidays in the next 90 days
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>