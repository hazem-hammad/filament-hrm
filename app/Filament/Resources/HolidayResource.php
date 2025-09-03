<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Filament\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'HR Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        Forms\Components\Section::make('Holiday Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Holiday Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if (!$get('end_date') || $state > $get('end_date')) {
                                            $set('end_date', $state);
                                        }
                                    }),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->required()
                                    ->native(false)
                                    ->minDate(fn (Get $get) => $get('start_date')),

                                Forms\Components\Select::make('type')
                                    ->label('Holiday Type')
                                    ->options([
                                        'public' => 'Public Holiday',
                                        'religious' => 'Religious Holiday',
                                        'national' => 'National Holiday',
                                        'company' => 'Company Holiday',
                                    ])
                                    ->default('public')
                                    ->required()
                                    ->native(false),

                                Forms\Components\ColorPicker::make('color')
                                    ->label('Calendar Color')
                                    ->default('#3B82F6'),
                            ])
                            ->columns(2)
                            ->columnSpan(1),

                        Forms\Components\Section::make('Holiday Settings')
                            ->schema([
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Enable/disable this holiday'),

                                Forms\Components\Toggle::make('is_paid')
                                    ->label('Paid Holiday')
                                    ->default(true)
                                    ->helperText('Employees get paid for this holiday'),

                                Forms\Components\Toggle::make('is_recurring')
                                    ->label('Recurring Holiday')
                                    ->default(false)
                                    ->live()
                                    ->helperText('Holiday repeats annually'),

                                Forms\Components\Select::make('recurrence_type')
                                    ->label('Recurrence Type')
                                    ->options([
                                        'none' => 'No Recurrence',
                                        'yearly' => 'Yearly',
                                        'monthly' => 'Monthly',
                                    ])
                                    ->default('none')
                                    ->visible(fn (Get $get) => $get('is_recurring'))
                                    ->native(false),

                                Forms\Components\CheckboxList::make('departments')
                                    ->label('Applicable Departments')
                                    ->helperText('Leave empty to apply to all departments')
                                    ->options(
                                        Department::where('status', true)
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('')
                    ->width(20),

                Tables\Columns\TextColumn::make('name')
                    ->label('Holiday Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('formatted_date_range')
                    ->label('Date Range')
                    ->sortable(['start_date'])
                    ->description(fn (Holiday $record): string => 
                        $record->duration > 1 ? $record->duration . ' days' : '1 day'
                    ),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'religious' => 'info',
                        'national' => 'danger',
                        'company' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Public',
                        'religious' => 'Religious',
                        'national' => 'National',
                        'company' => 'Company',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Recurring')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('departments')
                    ->label('Departments')
                    ->formatStateUsing(function ($state, Holiday $record) {
                        // Handle the case where departments is null or empty
                        if (is_null($state) || empty($state)) {
                            return 'All Departments';
                        }
                        
                        // Ensure $state is an array (in case it comes as JSON string)
                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }
                        
                        // If still not an array or empty, return all departments
                        if (!is_array($state) || empty($state)) {
                            return 'All Departments';
                        }
                        
                        $departmentNames = Department::whereIn('id', $state)->pluck('name');
                        return $departmentNames->take(2)->implode(', ') . 
                            ($departmentNames->count() > 2 ? ' +' . ($departmentNames->count() - 2) . ' more' : '');
                    })
                    ->wrap(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Holiday Type')
                    ->options([
                        'public' => 'Public Holiday',
                        'religious' => 'Religious Holiday',
                        'national' => 'National Holiday',
                        'company' => 'Company Holiday',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Paid Holiday')
                    ->boolean()
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_recurring')
                    ->label('Recurring')
                    ->boolean()
                    ->trueLabel('Recurring')
                    ->falseLabel('One-time')
                    ->native(false),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start_date'] ?? null, 
                                fn (Builder $query, $date): Builder => $query->where('start_date', '>=', $date))
                            ->when($data['end_date'] ?? null, 
                                fn (Builder $query, $date): Builder => $query->where('end_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn (Holiday $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn (Holiday $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Holiday $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(fn (Holiday $record): string => 
                            $record->status 
                                ? 'Are you sure you want to deactivate this holiday?' 
                                : 'Are you sure you want to activate this holiday?'
                        )
                        ->action(fn (Holiday $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate_selected')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => false]));
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'view' => Pages\ViewHoliday::route('/{record}'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
