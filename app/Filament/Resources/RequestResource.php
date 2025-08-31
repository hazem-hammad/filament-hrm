<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\AttendanceType;
use App\Models\Employee;
use App\Models\Request;
use App\Models\VacationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'HR Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Request Details Section
                        Forms\Components\Section::make('Request Details')
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('request_type')
                                    ->label('Request Type')
                                    ->options(Request::REQUEST_TYPES)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('requestable_id', null);
                                        $set('requestable_type', null);
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('requestable_id')
                                    ->label(function (Forms\Get $get) {
                                        return $get('request_type') === 'vacation' ? 'Vacation Type' : 'Attendance Type';
                                    })
                                    ->options(function (Forms\Get $get) {
                                        if ($get('request_type') === 'vacation') {
                                            return VacationType::where('status', true)->pluck('name', 'id');
                                        } elseif ($get('request_type') === 'attendance') {
                                            return AttendanceType::where('status', true)->pluck('name', 'id');
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        if ($state && $get('request_type')) {
                                            if ($get('request_type') === 'vacation') {
                                                $set('requestable_type', VacationType::class);
                                            } else {
                                                $set('requestable_type', AttendanceType::class);
                                            }
                                        }
                                    })
                                    ->visible(fn(Forms\Get $get) => filled($get('request_type')))
                                    ->columnSpanFull(),

                                Forms\Components\Hidden::make('requestable_type'),

                                // Balance Display
                                Forms\Components\Placeholder::make('balance_info')
                                    ->label('Remaining Balance')
                                    ->content(function (Forms\Get $get) {
                                        if (!$get('employee_id') || !$get('requestable_id') || !$get('request_type')) {
                                            return 'Select employee and type to see balance';
                                        }

                                        $employee = Employee::find($get('employee_id'));
                                        if (!$employee) return 'Employee not found';

                                        if ($get('request_type') === 'vacation') {
                                            $vacationType = VacationType::find($get('requestable_id'));
                                            if (!$vacationType) return 'Vacation type not found';

                                            // Calculate remaining balance
                                            $currentYear = now()->year;
                                            $usedDays = Request::where('employee_id', $employee->id)
                                                ->where('requestable_type', VacationType::class)
                                                ->where('requestable_id', $vacationType->id)
                                                ->where('status', 'approved')
                                                ->whereYear('start_date', $currentYear)
                                                ->sum('total_days');

                                            $remaining = max(0, $vacationType->balance - $usedDays);

                                            return "🏖️ {$remaining} days remaining out of {$vacationType->balance} annual days";
                                        } else {
                                            $attendanceType = AttendanceType::find($get('requestable_id'));
                                            if (!$attendanceType) return 'Attendance type not found';

                                            if (!$attendanceType->has_limit) {
                                                return '♾️ Unlimited';
                                            }

                                            // Calculate monthly usage
                                            $currentMonth = now()->format('Y-m');
                                            $monthlyUsage = Request::where('employee_id', $employee->id)
                                                ->where('requestable_type', AttendanceType::class)
                                                ->where('requestable_id', $attendanceType->id)
                                                ->where('status', 'approved')
                                                ->where('request_date', 'like', $currentMonth . '%')
                                                ->selectRaw('SUM(hours) as total_hours, COUNT(*) as total_requests')
                                                ->first();

                                            $usedHours = $monthlyUsage->total_hours ?? 0;
                                            $usedRequests = $monthlyUsage->total_requests ?? 0;

                                            $info = [];
                                            if ($attendanceType->max_hours_per_month) {
                                                $remainingHours = max(0, $attendanceType->max_hours_per_month - $usedHours);
                                                $info[] = "⏰ {$remainingHours}h remaining this month";
                                            }
                                            if ($attendanceType->max_requests_per_month) {
                                                $remainingRequests = max(0, $attendanceType->max_requests_per_month - $usedRequests);
                                                $info[] = "📝 {$remainingRequests} requests remaining this month";
                                            }

                                            return empty($info) ? '♾️ No monthly limits' : implode(' • ', $info);
                                        }
                                    })
                                    ->visible(fn(Forms\Get $get) => filled($get('requestable_id')))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // Vacation Fields Section
                        Forms\Components\Section::make('Vacation Details')
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        if ($state && $get('end_date')) {
                                            $start = \Carbon\Carbon::parse($state);
                                            $end = \Carbon\Carbon::parse($get('end_date'));
                                            $set('total_days', $start->diffInDays($end) + 1);
                                        }
                                    })
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $_attribute, $value, \Closure $fail) use ($get) {
                                                if (!$value || !$get('requestable_id') || !$get('employee_id')) {
                                                    return;
                                                }

                                                $vacationType = VacationType::find($get('requestable_id'));
                                                if (!$vacationType) return;

                                                if ($vacationType->required_days_before > 0) {
                                                    $noticeDate = now()->addDays($vacationType->required_days_before);
                                                    if (\Carbon\Carbon::parse($value)->lt($noticeDate)) {
                                                        $fail("This vacation type requires {$vacationType->required_days_before} days advance notice.");
                                                    }
                                                }

                                                $employee = Employee::find($get('employee_id'));
                                                if ($employee && !$vacationType->isAvailableForEmployee($employee)) {
                                                    $fail("This vacation type is not yet available. You need to wait {$vacationType->unlock_after_months} months after joining.");
                                                }
                                            };
                                        }
                                    ]),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        if ($state && $get('start_date')) {
                                            $start = \Carbon\Carbon::parse($get('start_date'));
                                            $end = \Carbon\Carbon::parse($state);
                                            $set('total_days', $start->diffInDays($end) + 1);
                                        }
                                    })
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $_attribute, $value, \Closure $fail) use ($get) {
                                                if (!$value || !$get('start_date')) {
                                                    return;
                                                }

                                                $startDate = \Carbon\Carbon::parse($get('start_date'));
                                                $endDate = \Carbon\Carbon::parse($value);

                                                if ($endDate->lt($startDate)) {
                                                    $fail('End date must be after or equal to start date.');
                                                }
                                            };
                                        }
                                    ]),
                                Forms\Components\TextInput::make('total_days')
                                    ->label('Total Days')
                                    ->numeric()
                                    ->disabled()
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $_attribute, $value, \Closure $fail) use ($get) {
                                                if (!$value || !$get('requestable_id') || !$get('employee_id')) {
                                                    return;
                                                }

                                                $vacationType = VacationType::find($get('requestable_id'));
                                                $employee = Employee::find($get('employee_id'));

                                                if (!$vacationType || !$employee) return;

                                                $currentYear = now()->year;
                                                $usedDays = Request::where('employee_id', $employee->id)
                                                    ->where('requestable_type', VacationType::class)
                                                    ->where('requestable_id', $vacationType->id)
                                                    ->where('status', 'approved')
                                                    ->whereYear('start_date', $currentYear)
                                                    ->sum('total_days');

                                                $remainingBalance = max(0, $vacationType->balance - $usedDays);

                                                if ($value > $remainingBalance) {
                                                    $fail("Insufficient balance. You have {$remainingBalance} days remaining for this vacation type.");
                                                }
                                            };
                                        }
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('request_type') === 'vacation')
                            ->columns(2),

                        // Attendance Fields Section
                        Forms\Components\Section::make('Attendance Details')
                            ->schema([
                                Forms\Components\DatePicker::make('request_date')
                                    ->label('Request Date')
                                    ->required()
                                    ->minDate(now()->toDateString())
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('hours')
                                    ->label('Number of Hours')
                                    ->numeric()
                                    ->step(0.25)
                                    ->minValue(0.25)
                                    ->maxValue(24)
                                    ->required()
                                    ->suffix('hours')
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $_attribute, $value, \Closure $fail) use ($get) {
                                                if (!$value || !$get('requestable_id') || !$get('employee_id')) {
                                                    return;
                                                }

                                                $attendanceType = AttendanceType::find($get('requestable_id'));
                                                $employee = Employee::find($get('employee_id'));

                                                if (!$attendanceType || !$employee) return;

                                                if ($attendanceType->has_limit) {
                                                    $currentMonth = now()->format('Y-m');
                                                    $monthlyUsage = Request::where('employee_id', $employee->id)
                                                        ->where('requestable_type', AttendanceType::class)
                                                        ->where('requestable_id', $attendanceType->id)
                                                        ->where('status', 'approved')
                                                        ->where('request_date', 'like', $currentMonth . '%')
                                                        ->selectRaw('SUM(hours) as total_hours, COUNT(*) as total_requests')
                                                        ->first();

                                                    $usedHours = $monthlyUsage->total_hours ?? 0;
                                                    $usedRequests = $monthlyUsage->total_requests ?? 0;

                                                    if ($attendanceType->max_hours_per_request && $value > $attendanceType->max_hours_per_request) {
                                                        $fail("Request exceeds maximum hours per request ({$attendanceType->max_hours_per_request} hours).");
                                                    }

                                                    if ($attendanceType->max_hours_per_month) {
                                                        $totalHoursAfterRequest = $usedHours + $value;
                                                        if ($totalHoursAfterRequest > $attendanceType->max_hours_per_month) {
                                                            $remaining = $attendanceType->max_hours_per_month - $usedHours;
                                                            $fail("Monthly hour limit exceeded. You have {$remaining} hours remaining this month.");
                                                        }
                                                    }

                                                    if ($attendanceType->max_requests_per_month) {
                                                        $totalRequestsAfterRequest = $usedRequests + 1;
                                                        if ($totalRequestsAfterRequest > $attendanceType->max_requests_per_month) {
                                                            $remaining = $attendanceType->max_requests_per_month - $usedRequests;
                                                            $fail("Monthly request limit exceeded. You have {$remaining} requests remaining this month.");
                                                        }
                                                    }
                                                }
                                            };
                                        }
                                    ]),
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->seconds(false),
                                Forms\Components\TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->seconds(false),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('request_type') === 'attendance')
                            ->columns(2),

                        // Additional Information Section
                        Forms\Components\Section::make('Additional Information')
                            ->schema([
                                Forms\Components\Textarea::make('reason')
                                    ->label('Reason')
                                    ->placeholder('Please provide a reason for this request...')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options(Request::STATUSES)
                                    ->default('pending')
                                    ->required()
                                    ->visible(fn(string $operation) => $operation === 'edit'),
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->placeholder('Admin notes or rejection reason...')
                                    ->rows(3)
                                    ->visible(fn(string $operation) => $operation === 'edit'),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
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
        ];
    }
}
