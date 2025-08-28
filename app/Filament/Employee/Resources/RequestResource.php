<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\RequestResource\Pages;
use App\Models\Request;
use App\Models\VacationType;
use App\Models\AttendanceType;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

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
                Forms\Components\Select::make('request_type')
                    ->label('Request Type')
                    ->options([
                        'vacation' => 'Vacation',
                        'attendance' => 'Attendance',
                    ])
                    ->required()
                    ->columnSpanFull()
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
                    }),

                Forms\Components\Select::make('requestable_id')
                    ->label(fn($get) => $get('request_type') === 'vacation' ? 'Vacation Type' : 'Attendance Type')
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

                // Display remaining balance information
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
                        }

                        if ($get('request_type') === 'attendance') {
                            $attendanceType = AttendanceType::find($get('requestable_id'));
                            if (!$attendanceType) return 'Attendance type not found';

                            if (!$attendanceType->has_limit) {
                                return '♾️ No monthly limits';
                            }

                            // Calculate monthly usage
                            $currentMonth = now()->format('Y-m');
                            $monthlyUsage = Request::where('employee_id', $employee->id)
                                ->where('requestable_type', AttendanceType::class)
                                ->where('requestable_id', $attendanceType->id)
                                ->where('status', 'approved')
                                ->where('request_date', 'like', "{$currentMonth}%")
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

                        return '';
                    })
                    ->columnSpanFull()
                    ->visible(fn($get) => !empty($get('requestable_id'))),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->visible(fn($get) => $get('request_type') === 'vacation')
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state && $get('end_date')) {
                                    $start = \Carbon\Carbon::parse($state);
                                    $end = \Carbon\Carbon::parse($get('end_date'));
                                    $set('total_days', $start->diffInDays($end) + 1);
                                }
                            }),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->visible(fn($get) => $get('request_type') === 'vacation')
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state && $get('start_date')) {
                                    $start = \Carbon\Carbon::parse($get('start_date'));
                                    $end = \Carbon\Carbon::parse($state);
                                    $set('total_days', $start->diffInDays($end) + 1);
                                }
                            }),
                    ])
                    ->visible(fn($get) => $get('request_type') === 'vacation'),

                Forms\Components\TextInput::make('total_days')
                    ->label('Total Days')
                    ->disabled()
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('request_type') === 'vacation'),

                Forms\Components\DatePicker::make('request_date')
                    ->label('Request Date')
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('request_type') === 'attendance'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->visible(fn($get) => $get('request_type') === 'attendance'),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->visible(fn($get) => $get('request_type') === 'attendance')
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state && $get('start_time')) {
                                    $start = \Carbon\Carbon::parse($get('start_time'));
                                    $end = \Carbon\Carbon::parse($state);
                                    $hours = $end->diffInHours($start, true);
                                    $set('hours', number_format($hours, 2));
                                }
                            }),
                    ])
                    ->visible(fn($get) => $get('request_type') === 'attendance'),

                Forms\Components\TextInput::make('hours')
                    ->label('Total Hours')
                    ->disabled()
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('request_type') === 'attendance'),

                Forms\Components\Textarea::make('reason')
                    ->label('Reason')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('admin_notes')
                    ->label(fn($record) => $record && $record->status === 'rejected' ? 'Rejection Reason' : 'Admin Notes')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled()
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
                    ->tooltip(fn ($record) => $record && $record->admin_notes ? $record->admin_notes : null)
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
