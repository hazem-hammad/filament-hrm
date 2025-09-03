<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'HR Management';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Details')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'name', fn($query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state) {
                                    $employee = Employee::find($state);
                                    $workPlan = $employee?->workPlans()->active()->first();
                                    if ($workPlan) {
                                        $set('work_plan_id', $workPlan->id);
                                        $set('work_plan_display', $workPlan->name);
                                    } else {
                                        $set('work_plan_display', 'No work plan assigned');
                                    }
                                }
                            }),
                        Forms\Components\Placeholder::make('work_plan_display')
                            ->label('Assigned Work Plan')
                            ->content(fn($get) => $get('work_plan_display') ?? 'Select an employee first'),
                        Forms\Components\Hidden::make('work_plan_id'),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->default(today())
                            ->maxDate(today()),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Time Details')
                    ->schema([
                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Check In Time')
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('check_out_time')
                            ->label('Check Out Time')
                            ->seconds(false)
                            ->after('check_in_time'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Add any notes about this attendance record')
                            ->columnSpanFull()
                            ->rows(3),
                        Forms\Components\Hidden::make('is_manual')
                            ->default(true),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
