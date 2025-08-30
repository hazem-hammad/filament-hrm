@if($this->getViewData()['shouldShow'])
<x-filament-widgets::widget>
    <div class="relative overflow-hidden bg-gradient-to-br from-pink-400 via-purple-500 to-indigo-600 rounded-2xl shadow-2xl">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="birthday-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="1.5" fill="white" opacity="0.3"/>
                        <path d="M8 6 L10 4 L12 6" stroke="white" stroke-width="0.5" fill="none" opacity="0.2"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#birthday-pattern)"/>
            </svg>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-4 right-4 animate-bounce">
            <div class="text-yellow-300 text-2xl">🎂</div>
        </div>
        <div class="absolute top-8 left-6 animate-pulse">
            <div class="text-pink-200 text-xl">🎈</div>
        </div>
        <div class="absolute bottom-8 right-8 animate-bounce" style="animation-delay: 0.5s;">
            <div class="text-yellow-300 text-lg">✨</div>
        </div>
        <div class="absolute bottom-12 left-8 animate-pulse" style="animation-delay: 1s;">
            <div class="text-pink-200 text-xl">🎉</div>
        </div>

        <!-- Main Content -->
        <div class="relative p-8">
            <div class="text-center">
                <!-- Birthday Message -->
                <div class="mb-6">
                    <h1 class="text-4xl font-bold text-white mb-2 animate-pulse">
                        🎉 Happy Birthday! 🎉
                    </h1>
                    <p class="text-white/90 text-lg">
                        Hope your special day is absolutely wonderful!
                    </p>
                </div>

                <!-- Profile Section -->
                <div class="flex flex-col items-center mb-6">
                    <div class="relative mb-4">
                        <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm p-1 shadow-2xl">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-white/30">
                                @if($this->getViewData()['profileImage'])
                                    <img src="{{ $this->getViewData()['profileImage'] }}" 
                                         alt="{{ $this->getViewData()['employee']->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($this->getViewData()['employee']->name) }}&color=FFFFFF&background=8B5CF6&size=200" 
                                         alt="{{ $this->getViewData()['employee']->name }}" 
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                        <!-- Birthday Crown -->
                        <div class="absolute -top-2 left-1/2 transform -translate-x-1/2">
                            <div class="text-yellow-300 text-2xl animate-bounce">👑</div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-2">
                        {{ $this->getViewData()['employee']->name }}
                    </h2>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-6 py-2 border border-white/30">
                        <p class="text-white font-semibold text-lg">
                            🎂 Turning {{ $this->getViewData()['myAge'] }} Today!
                        </p>
                    </div>
                </div>

                <!-- Birthday Wishes -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div class="space-y-2">
                            <div class="text-3xl">🌟</div>
                            <h3 class="font-semibold text-white">Make a Wish</h3>
                            <p class="text-white/80 text-sm">May all your dreams come true</p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl">🎁</div>
                            <h3 class="font-semibold text-white">Celebrate</h3>
                            <p class="text-white/80 text-sm">Enjoy your special day</p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-3xl">🥳</div>
                            <h3 class="font-semibold text-white">Party Time</h3>
                            <p class="text-white/80 text-sm">Time to have some fun</p>
                        </div>
                    </div>
                </div>

                <!-- Fun Birthday Stats -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-white">{{ $this->getViewData()['myAge'] }}</div>
                            <div class="text-white/70 text-xs uppercase tracking-wide">Years Young</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">{{ number_format($this->getViewData()['daysLived']) }}</div>
                            <div class="text-white/70 text-xs uppercase tracking-wide">Days Lived</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">{{ now()->format('d') }}</div>
                            <div class="text-white/70 text-xs uppercase tracking-wide">Special Date</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">∞</div>
                            <div class="text-white/70 text-xs uppercase tracking-wide">More Adventures</div>
                        </div>
                    </div>
                </div>

                <!-- Birthday Message from Team -->
                <div class="mt-6 text-center">
                    <p class="text-white/90 italic">
                        "Wishing you a day filled with happiness and a year filled with joy!"
                    </p>
                    <p class="text-white/70 text-sm mt-2">
                        - From your HRM Team 💝
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
@endif