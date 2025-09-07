@if (!$hasQrCode)
    <div class="p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-lg">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5H8.25v1.5H13.5V13.5zM13.5 16.5H8.25V18H13.5v-1.5zM16.5 13.5h1.5V15h-1.5v-1.5zM16.5 16.5h1.5V18h-1.5v-1.5z"/>
        </svg>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">QR Code will be generated after saving</p>
    </div>
@else
    @if ($qrUrl)
        <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg border">
            <img src="{{ $qrUrl }}" alt="QR Code Preview" class="mx-auto mb-2" style="max-width: 250px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <p class="text-sm text-gray-600 dark:text-gray-400">Preview of your customized QR code</p>
            <p class="text-xs text-gray-500 mt-1">Scan with your phone to test</p>
        </div>
    @else
        <div class="p-4 text-center bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
            <svg class="mx-auto h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">QR Code preview unavailable</p>
        </div>
    @endif
@endif