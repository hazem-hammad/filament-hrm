<x-filament-widgets::widget>
    <x-filament::section class="h-96">
        <div class="p-2 h-full flex flex-col overflow-hidden">
            <!-- Header with date and time -->
            <div class="flex justify-between items-center mb-3 text-sm text-gray-500">
                <span>{{ $todayDate }}</span>
                <span>{{ $currentTime }}</span>
            </div>

            <!-- Greeting -->
            <div class="text-center">
                <p class="text-gray-200 font-bold mb-1">{{ $greeting }} {{ $employeeName }}</p>

                @if ($canCheckIn)
                    <h2 class="text-xl font-semibold text-white-800 mt-8 mb-6"">
                        Let's get to work! 👋
                    </h2>
                @elseif ($hasCheckedOut)
                    <h2 class="text-xl font-semibold text-white-800 mt-8 mb-6">
                        Work day completed! 🎉
                    </h2>
                @endif
            </div>

            <!-- Check In/Out Button -->
            <div class="text-center flex-1 flex items-center justify-center">
                @if ($canCheckIn)
                    <!-- Check In State - No attendance record for today -->
                    <div class="inline-flex flex-col items-center gap-3 mt-4">
                        <button type="button" wire:click="mountAction('checkIn')"
                            class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-2xl transform hover:scale-105 transition-transform duration-200 focus:outline-none focus:ring-4 focus:ring-green-300/40"
                            aria-label="Check In" title="Check In">
                            <svg class="w-10 h-10 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path
                                    d="M12 2a1 1 0 011 1v8h8a1 1 0 110 2h-8v8a1 1 0 11-2 0v-8H3a1 1 0 110-2h8V3a1 1 0 011-1z" />
                            </svg>
                        </button>

                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-100">Ready to start?</div>
                            <p class="text-sm text-gray-300">Tap to mark your check-in for today.</p>
                        </div>
                    </div>
                @elseif ($isCheckedIn)
                    <!-- Checked In State - Has check-in but no check-out -->
                    <div class="inline-flex flex-col items-center gap-6 mt-4">
                        <div class="flex flex-col items-center">
                            <div class="text-sm text-gray-300 mb-1">Checked in at:
                                <span class="font-bold"> {{ $checkInTime }} </span>
                            </div>
                        </div>

                        <div class="relative">
                            <button type="button" wire:click="mountAction('checkOut')"
                                class="w-24 h-24 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center shadow-2xl transform hover:scale-105 transition-transform duration-200 focus:outline-none focus:ring-4 focus:ring-red-300/40"
                                aria-label="Check Out" title="Check Out">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>

                            <span
                                class="absolute -top-3 -right-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-yellow-900 shadow">
                                Live
                            </span>
                        </div>

                        <div class="flex flex-col text-center" x-data="{ 
                            duration: '{{ $workDuration }}',
                            checkInTime: '{{ $checkInTime }}',
                            startTimer() {
                                if (this.checkInTime) {
                                    const [hours, minutes] = this.checkInTime.split(':').map(Number);
                                    const checkIn = new Date();
                                    checkIn.setHours(hours, minutes, 0, 0);
                                    
                                    const updateDuration = () => {
                                        const now = new Date();
                                        const diff = now - checkIn;
                                        const totalSeconds = Math.floor(diff / 1000);
                                        const h = Math.floor(totalSeconds / 3600);
                                        const m = Math.floor((totalSeconds % 3600) / 60);
                                        const s = totalSeconds % 60;
                                        
                                        this.duration = String(h).padStart(2, '0') + ':' + 
                                                       String(m).padStart(2, '0') + ':' + 
                                                       String(s).padStart(2, '0');
                                    };
                                    
                                    updateDuration();
                                    setInterval(updateDuration, 1000);
                                }
                            }
                        }" x-init="startTimer()">
                            <div class="text-sm text-gray-300 mb-1">Duration</div>
                            <div class="text-lg font-medium text-white-100" aria-live="polite" x-text="duration">
                                {{ $workDuration }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Keep working — don't forget to check out when done.
                            </p>
                        </div>
                    </div>
                @elseif ($hasCheckedOut)
                    <!-- Checked Out State - Has both check-in and check-out -->
                    <div class="inline-flex flex-col items-center gap-3">
                        <div
                            class="relative w-20 h-20 rounded-full flex items-center justify-center bg-gradient-to-br from-green-400 to-teal-500 shadow-xl">
                            <!-- Check icon -->
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2.5" d="M7 13l3 3 7-9" />
                            </svg>
                        </div>

                        <div class="text-center">
                            <p class="text-lg font-semibold text-white mb-1">Work Completed</p>
                            <p class="text-sm text-gray-300">Total: <span
                                    class="text-emerald-300 font-bold">{{ $workDuration }}</span></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>

    <!-- Action Modals -->
    <x-filament-actions::modals />


    <style>
        /* Custom styles for the circular buttons */
        button[wire\:click] {
            outline: none;
        }

        button[wire\:click]:focus {
            outline: 2px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }
    </style>
</x-filament-widgets::widget>
