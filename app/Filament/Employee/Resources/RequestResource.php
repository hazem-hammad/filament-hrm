<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\RequestResource\Pages;
use App\Models\Request;
use App\Models\VacationType;
use App\Models\AttendanceType;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Requests';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header Section with Request Type Selection
                Forms\Components\Section::make('Request Information')
                    ->description('Select the type of request you would like to submit')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('request_type')
                                    ->label('Request Type')
                                    ->placeholder('Choose request type...')
                                    ->options([
                                        'vacation' => '🏖️ Vacation Request',
                                        'attendance' => '⏰ Attendance Request',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($set) {
                                        // Reset form fields when request type changes
                                        $set('requestable_id', null);
                                        $set('start_date', null);
                                        $set('end_date', null);
                                        $set('request_date', null);
                                        $set('hours', null);
                                        $set('start_time', null);
                                        $set('end_time', null);
                                    })
                                    ->helperText('Select whether you want to request vacation time or attendance adjustment'),

                                Forms\Components\Placeholder::make('request_guide')
                                    ->label('')
                                    ->content(function ($get) {
                                        return match ($get('request_type')) {
                                            'vacation' => '📋 **Vacation requests** are for planned time off such as holidays, personal leave, sick days, or other approved absences.',
                                            'attendance' => '📋 **Attendance requests** are for adjustments to your work schedule such as flexible hours, overtime, or schedule changes.',
                                            default => '💡 **Choose a request type** to see specific guidelines and available options.',
                                        };
                                    })
                                    ->columnSpan(1)
                                    ->visible(fn($get) => !empty($get('request_type'))),
                            ]),
                    ])
                    ->columnSpan(2),

                // Category Selection Section
                Forms\Components\Section::make('Category Selection')
                    ->description('Choose the specific category for your request')
                    ->icon(fn($get) => $get('request_type') === 'vacation' ? 'heroicon-o-sun' : 'heroicon-o-clock')
                    ->schema([

                        Forms\Components\Select::make('requestable_id')
                            ->label(fn($get) => $get('request_type') === 'vacation' ? 'Vacation Type' : 'Attendance Type')
                            ->placeholder(fn($get) => $get('request_type') === 'vacation' ? 'Select vacation type...' : 'Select attendance type...')
                            ->options(function ($get) {
                                if ($get('request_type') === 'vacation') {
                                    return VacationType::query()->active()->pluck('name', 'id');
                                }
                                if ($get('request_type') === 'attendance') {
                                    return AttendanceType::query()->active()->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->visible(fn($get) => !empty($get('request_type')))
                            ->live(),

                        // Enhanced Balance Information Card
                        Forms\Components\Placeholder::make('balance_info')
                            ->label('')
                            ->content(function ($get) {
                                if (!$get('requestable_id') || !$get('request_type')) {
                                    return '';
                                }

                                /** @var \App\Models\Employee $employee */
                                $employee = auth('employee')->user();

                                if ($get('request_type') === 'vacation') {
                                    $vacationType = VacationType::find($get('requestable_id'));
                                    if (!$vacationType) return '❌ Vacation type not found';

                                    // Calculate remaining balance
                                    $currentYear = now()->year;
                                    $usedDays = Request::where('employee_id', $employee->id)
                                        ->where('requestable_type', VacationType::class)
                                        ->where('requestable_id', $vacationType->id)
                                        ->where('status', 'approved')
                                        ->whereYear('start_date', $currentYear)
                                        ->sum('total_days');

                                    $remaining = max(0, $vacationType->balance - $usedDays);
                                    $percentage = $vacationType->balance > 0 ? round(($remaining / $vacationType->balance) * 100) : 0;

                                    return new HtmlString("
                                    <div class='bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4'>
                                        <div class='flex items-center gap-2 mb-2'>
                                            <span class='text-2xl'>🏖️</span>
                                            <h3 class='font-semibold text-blue-800 dark:text-blue-200'>Vacation Balance</h3>
                                        </div>
                                        <div class='space-y-2'>
                                            <div class='flex justify-between text-sm'>
                                                <span class='text-gray-700 dark:text-gray-300'>Remaining Days:</span>
                                                <span class='font-bold text-blue-600 dark:text-blue-400'>{$remaining} / {$vacationType->balance}</span>
                                            </div>
                                            <div class='w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2'>
                                                <div class='bg-blue-600 dark:bg-blue-400 h-2 rounded-full' style='width: {$percentage}%'></div>
                                            </div>
                                            <p class='text-xs text-blue-600 dark:text-blue-400'>Used: {$usedDays} days this year</p>
                                        </div>
                                    </div>
                                    ");
                                }

                                if ($get('request_type') === 'attendance') {
                                    $attendanceType = AttendanceType::find($get('requestable_id'));
                                    if (!$attendanceType) return new HtmlString('❌ Attendance type not found');

                                    // Use the new monthly validation method
                                    $requestedHours = (float)($get('hours') ?? 0);
                                    $validation = $attendanceType->canEmployeeRequestThisMonth($employee->id, $requestedHours);

                                    if (!$attendanceType->has_limit) {
                                        return new HtmlString("
                                        <div class='bg-green-50 border border-green-200 rounded-lg p-4'>
                                            <div class='flex items-center gap-2'>
                                                <span class='text-2xl'>♾️</span>
                                                <h3 class='font-semibold text-green-800'>No Monthly Limits</h3>
                                            </div>
                                            <p class='text-sm text-green-600 mt-1'>This attendance type has no restrictions</p>
                                            <div class='text-xs text-gray-600 mt-2'>
                                                Current month usage: {$validation['used_requests']} requests, {$validation['used_hours']} hours
                                            </div>
                                        </div>
                                        ");
                                    }

                                    // Determine status color based on validation  
                                    $statusColor = $validation['can_request'] ? 'blue' : 'red';
                                    $statusIcon = $validation['can_request'] ? '⏰' : '⚠️';

                                    $content = "<div class='bg-{$statusColor}-50 dark:bg-{$statusColor}-900 border border-{$statusColor}-200 dark:border-{$statusColor}-700 rounded-lg p-4'>";
                                    $content .= "<div class='flex items-center gap-2 mb-2'><span class='text-2xl'>{$statusIcon}</span><h3 class='font-semibold text-{$statusColor}-800 dark:text-{$statusColor}-200'>Monthly Usage - " . now()->format('F Y') . "</h3></div>";

                                    if (!$validation['can_request']) {
                                        $content .= "<div class='mb-3 p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded text-red-700 dark:text-red-200 text-sm'>";
                                        $content .= "❌ {$validation['message']}";
                                        $content .= "</div>";
                                    }

                                    $content .= "<div class='space-y-2'>";

                                    if ($attendanceType->max_hours_per_month) {
                                        $usedHours = $validation['used_hours'];
                                        $remainingHours = $validation['remaining_hours'] ?? 0;
                                        $hourPercentage = $attendanceType->max_hours_per_month > 0 ? round(($usedHours / $attendanceType->max_hours_per_month) * 100) : 0;
                                        $barColor = $validation['can_request'] ? 'blue' : 'red';
                                        $content .= "
                                        <div>
                                            <div class='flex justify-between text-sm'>
                                                <span class='text-gray-700 dark:text-gray-300'>Hours Used:</span>
                                                <span class='font-bold text-{$barColor}-600 dark:text-{$barColor}-400'>{$usedHours} / {$attendanceType->max_hours_per_month}h</span>
                                            </div>
                                            <div class='w-full bg-{$barColor}-200 dark:bg-{$barColor}-800 rounded-full h-2'>
                                                <div class='bg-{$barColor}-600 dark:bg-{$barColor}-400 h-2 rounded-full' style='width: {$hourPercentage}%'></div>
                                            </div>
                                            <p class='text-xs text-{$barColor}-600 dark:text-{$barColor}-400'>Remaining: {$remainingHours}h</p>
                                        </div>";
                                    }

                                    if ($attendanceType->max_requests_per_month) {
                                        $usedRequests = $validation['used_requests'];
                                        $remainingRequests = $validation['remaining_requests'] ?? 0;
                                        $requestPercentage = $attendanceType->max_requests_per_month > 0 ? round(($usedRequests / $attendanceType->max_requests_per_month) * 100) : 0;
                                        $barColor = $validation['can_request'] ? 'blue' : 'red';
                                        $content .= "
                                        <div>
                                            <div class='flex justify-between text-sm'>
                                                <span class='text-gray-700 dark:text-gray-300'>Requests Used:</span>
                                                <span class='font-bold text-{$barColor}-600 dark:text-{$barColor}-400'>{$usedRequests} / {$attendanceType->max_requests_per_month}</span>
                                            </div>
                                            <div class='w-full bg-{$barColor}-200 dark:bg-{$barColor}-800 rounded-full h-2'>
                                                <div class='bg-{$barColor}-600 dark:bg-{$barColor}-400 h-2 rounded-full' style='width: {$requestPercentage}%'></div>
                                            </div>
                                            <p class='text-xs text-{$barColor}-600 dark:text-{$barColor}-400'>Remaining: {$remainingRequests}</p>
                                        </div>";
                                    }

                                    $content .= "</div></div>";
                                    return new HtmlString($content);
                                }

                                return '';
                            })
                            ->columnSpanFull()
                            ->visible(fn($get) => !empty($get('requestable_id'))),
                    ])
                    ->visible(fn($get) => !empty($get('request_type')))
                    ->columnSpan(2),

                // Vacation Date Selection Section
                Forms\Components\Section::make('Vacation Dates')
                    ->description('Select your vacation start and end dates')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->placeholder('Select start date...')
                                    ->required()
                                    ->live()
                                    ->minDate(now()->addDay())
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        if ($state && $get('end_date')) {
                                            $start = \Carbon\Carbon::parse($state);
                                            $end = \Carbon\Carbon::parse($get('end_date'));
                                            if ($end->lt($start)) {
                                                $set('end_date', $state);
                                            }
                                            $set('total_days', $start->diffInDays(\Carbon\Carbon::parse($get('end_date'))) + 1);
                                        }
                                    })
                                    ->helperText('Must be at least 1 day in advance'),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->placeholder('Select end date...')
                                    ->required()
                                    ->live()
                                    ->minDate(fn($get) => $get('start_date') ?: now()->addDay())
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        if ($state && $get('start_date')) {
                                            $start = \Carbon\Carbon::parse($get('start_date'));
                                            $end = \Carbon\Carbon::parse($state);
                                            $set('total_days', $start->diffInDays($end) + 1);
                                        }
                                    })
                                    ->helperText('Must be after start date'),

                                Forms\Components\TextInput::make('total_days')
                                    ->label('Total Days')
                                    ->disabled()
                                    ->placeholder('Auto calculated')
                                    ->prefix('📊')
                                    ->live()
                                    ->helperText('Automatically calculated'),
                            ]),
                    ])
                    ->visible(fn($get) => $get('request_type') === 'vacation')
                    ->columnSpan(2),

                // Attendance Date/Time Selection Section
                Forms\Components\Section::make('Attendance Details')
                    ->description('Select the date and time for your attendance request')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\DatePicker::make('request_date')
                            ->label('Request Date')
                            ->placeholder('Select date...')
                            ->required()
                            ->minDate(now())
                            ->maxDate(now()->addMonths(3))
                            ->columnSpanFull()
                            ->helperText('Select the date for your attendance adjustment'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->placeholder('Select start time...')
                                    ->required()
                                    ->seconds(false)
                                    ->helperText('When you start work'),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->placeholder('Select end time...')
                                    ->required()
                                    ->seconds(false)
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        if ($state && $get('start_time')) {
                                            $start = \Carbon\Carbon::parse($get('start_time'));
                                            $end = \Carbon\Carbon::parse($state);
                                            if ($end->lte($start)) {
                                                return; // Don't update if end time is before start time
                                            }
                                            $hours = $end->diffInHours($start, true);
                                            $set('hours', number_format($hours, 2));
                                        }
                                    })
                                    ->helperText('When you end work'),

                                Forms\Components\TextInput::make('hours')
                                    ->label('Total Hours')
                                    ->disabled()
                                    ->placeholder('Auto calculated')
                                    ->prefix('⏱️')
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                if ($get('request_type') !== 'attendance' || !$get('requestable_id') || !$value) {
                                                    return;
                                                }

                                                $attendanceType = AttendanceType::find($get('requestable_id'));
                                                if (!$attendanceType) {
                                                    return;
                                                }

                                                $employee = Auth::guard('employee')->user();
                                                $validation = $attendanceType->canEmployeeRequestThisMonth($employee->id, (float)$value);

                                                if (!$validation['can_request']) {
                                                    $fail($validation['message']);
                                                }
                                            };
                                        },
                                    ])
                                    ->helperText('Automatically calculated'),
                            ]),
                    ])
                    ->visible(fn($get) => $get('request_type') === 'attendance')
                    ->columnSpan(2),

                // Reason and Documentation Section
                Forms\Components\Section::make('Request Details')
                    ->description('Provide the reason for your request and any supporting documentation')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for Request')
                            ->placeholder('Please provide a detailed explanation for your request...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Be specific about why you need this time off or schedule adjustment'),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('Supporting Documents')
                            ->collection('request_attachments')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxFiles(5)
                            ->maxSize(5120) // 5MB per file
                            ->columnSpanFull()
                            ->helperText('Optional: Upload medical certificates, travel documents, or other supporting files (Max 5 files, 5MB each)')
                            ->visible(fn($get) => $get('request_type') === 'vacation'),

                        // Manager Selection for Escalation
                        Forms\Components\Select::make('escalate_to')
                            ->label('Escalate to Manager (Optional)')
                            ->placeholder('Select manager for escalation...')
                            ->options(function () {
                                $employee = auth('employee')->user();
                                return \App\Models\Employee::where('id', '!=', $employee->id)
                                    ->whereHas('directReports')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText('Select a different manager if your direct manager is unavailable')
                            ->visible(fn($get) => !empty($get('reason'))),
                    ])
                    ->columnSpan(2),

                // Admin Notes (View Only)
                Forms\Components\Section::make('Administrative Notes')
                    ->description('Notes from your manager or HR department')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label(fn($record) => $record && $record->status === 'rejected' ? 'Rejection Reason' : 'Admin Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled()
                            ->helperText('This field is filled by administrators when reviewing your request'),
                    ])
                    ->columnSpan(2)
                    ->visible(fn($record) => $record && !empty($record->admin_notes)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'vacation' => 'info',
                        'attendance' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('requestable.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'attendance'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'attendance'),

                Tables\Columns\TextColumn::make('total_days')
                    ->label('Days')
                    ->numeric()
                    ->sortable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'attendance'),

                Tables\Columns\TextColumn::make('request_date')
                    ->label('Request Date')
                    ->date()
                    ->sortable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'vacation'),

                Tables\Columns\TextColumn::make('hours')
                    ->label('Hours')
                    ->numeric(2)
                    ->sortable()
                    ->visible(fn($livewire) => $livewire->activeTab !== 'vacation'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn($record) => $record && in_array($record->status, ['approved', 'rejected'])),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Decision Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn($record) => $record && in_array($record->status, ['approved', 'rejected'])),

                Tables\Columns\TextColumn::make('admin_notes')
                    ->label('Rejection Reason')
                    ->limit(50)
                    ->tooltip(fn($record) => $record && $record->admin_notes ? $record->admin_notes : null)
                    ->toggleable()
                    ->visible(fn($record) => $record && $record->status === 'rejected' && !empty($record->admin_notes)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('request_type')
                    ->options([
                        'vacation' => 'Vacation',
                        'attendance' => 'Attendance',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'pending' && $record->employee_id === auth('employee')->id()),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Request')
                    ->modalDescription('Are you sure you want to cancel this request?')
                    ->visible(fn($record) => $record->status === 'pending' && $record->employee_id === auth('employee')->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => false), // Disable bulk delete for requests
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'view' => Pages\ViewRequest::route('/{record}'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $employee = auth('employee')->user();

        // Get all requests that the employee can see (own requests + team requests if manager)
        $query = parent::getEloquentQuery()->with(['requestable', 'approver', 'employee']);

        // Include own requests
        $employeeIds = collect([$employee->id]);

        // Include team requests if employee is a manager
        if ($employee->directReports()->exists()) {
            $employeeIds = $employeeIds->merge($employee->directReports()->pluck('id'));
        }

        return $query->whereIn('employee_id', $employeeIds);
    }
}
