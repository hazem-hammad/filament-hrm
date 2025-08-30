<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center overflow-hidden">
                    @php
                        $employee = auth()->guard('employee')->user();
                        $profileImage = $employee?->getFirstMediaUrl('profile', 'thumb');
                    @endphp
                    
                    @if($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name ?? 'User') }}&color=7F9CF5&background=EBF4FF" 
                             alt="{{ $employee->name ?? 'User' }}" 
                             class="w-full h-full object-cover">
                    @endif
                </div>
                
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $employee->name ?? 'Employee' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $employee->position?->name ?? 'Position' }} • {{ $employee->department?->name ?? 'Department' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">
                        Employee ID: {{ $employee->employee_id ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <x-filament-panels::form wire:submit="updateProfile">
            {{ $this->form }}
            
            <x-filament-panels::form.actions :actions="$this->getFormActions()" />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>