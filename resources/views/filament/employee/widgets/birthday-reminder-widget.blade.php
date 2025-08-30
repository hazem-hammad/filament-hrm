<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-heroicon-o-cake class="w-5 h-5 text-pink-500" />
                <span>Upcoming Birthdays</span>
                @if ($this->getViewData()['totalUpcoming'] > 0)
                    <x-filament::badge color="pink" size="sm">
                        {{ $this->getViewData()['totalUpcoming'] }} in next 30 days
                    </x-filament::badge>
                @endif
            </div>
        </x-slot>

        <div>
            @if (!$this->getViewData()['hasAnyBirthdays'])
                <div class="text-center py-12">
                    <x-heroicon-o-cake class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        No Upcoming Birthdays
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        There are no employee birthdays in the next 30 days.
                    </p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                    @foreach ($this->getUpcomingBirthdays() as $employee)
                        @if ($employee->is_today)
                            {{-- Today's Birthday - Special styling --}}
                            <div
                                class="relative p-4 bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/20 dark:to-purple-900/20 rounded-xl border-2 border-pink-200 dark:border-pink-700 shadow-sm">
                                <div class="absolute -top-2 -right-2">
                                    <div class="bg-pink-500 text-white rounded-full p-1">
                                        <x-heroicon-s-cake class="w-4 h-4" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @php
                                            $profileImage = $employee->getFirstMediaUrl('profile', 'thumb');
                                        @endphp

                                        @if ($profileImage)
                                            <img src="{{ $profileImage }}" alt="{{ $employee->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&color=EC4899&background=FCE7F3"
                                                alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $employee->name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 truncate">
                                            {{ $employee->position?->name }} • {{ $employee->department?->name }}
                                        </p>
                                        <p class="text-xs font-medium text-pink-600 dark:text-pink-400 mt-1">
                                            🎂 Turning {{ $employee->turning_age }} today!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($employee->is_this_week)
                            {{-- This Week's Birthday - Blue styling --}}
                            <div
                                class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @php
                                            $profileImage = $employee->getFirstMediaUrl('profile', 'thumb');
                                        @endphp

                                        @if ($profileImage)
                                            <img src="{{ $profileImage }}" alt="{{ $employee->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&color=3B82F6&background=DBEAFE"
                                                alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $employee->name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 truncate">
                                            {{ $employee->position?->name }}
                                        </p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            {{ $employee->upcoming_birthday->format('M j') }} •
                                            {{ $employee->days_until_birthday }} days
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Future Birthday - Green styling --}}
                            <div
                                class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @php
                                            $profileImage = $employee->getFirstMediaUrl('profile', 'thumb');
                                        @endphp

                                        @if ($profileImage)
                                            <img src="{{ $profileImage }}" alt="{{ $employee->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&color=10B981&background=D1FAE5"
                                                alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $employee->name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 truncate">
                                            {{ $employee->position?->name }}
                                        </p>
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            {{ $employee->upcoming_birthday->format('M j') }} •
                                            {{ $employee->days_until_birthday }}d
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
