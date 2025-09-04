<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Enum\InsuranceRelation;
use App\Enum\InsuranceStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalInsuranceRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalInsurance';

    protected static ?string $recordTitleAttribute = 'insurance_number';

    protected static ?string $title = 'Medical Insurance';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        // Insurance Details Section
                        Forms\Components\Section::make('Insurance Details')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Select::make('insurance_status')
                                    ->label('Insurance Status')
                                    ->placeholder('Select insurance status')
                                    ->options(InsuranceStatus::options())
                                    ->default(InsuranceStatus::NOT_APPLICABLE->value)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state === InsuranceStatus::NOT_APPLICABLE->value) {
                                            $set('insurance_number', null);
                                            $set('insurance_relation', null);
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('insurance_number')
                                    ->label('Insurance Number')
                                    ->placeholder('Enter insurance policy number')
                                    ->maxLength(255)
                                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                                    
                                Forms\Components\Select::make('insurance_relation')
                                    ->label('Insurance Relation')
                                    ->placeholder('Select relationship type')
                                    ->options(InsuranceRelation::options())
                                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                            ])
                            ->columns(1),
                    ]),
                    
                // Cost Information Section
                Forms\Components\Section::make('Cost Information')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('annual_cost')
                                    ->label('Annual Cost')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('monthly_cost', round($state / 12, 2));
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('monthly_cost')
                                    ->label('Monthly Cost')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('annual_cost', round($state * 12, 2));
                                        }
                                    }),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible()
                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                    
                // Timeline Section
                Forms\Components\Section::make('Coverage Timeline')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('activation_date')
                                    ->label('Activation Date')
                                    ->placeholder('Select activation date'),
                                    
                                Forms\Components\DatePicker::make('deactivation_date')
                                    ->label('Deactivation Date')
                                    ->placeholder('Select deactivation date (optional)')
                                    ->after('activation_date'),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible()
                    ->visible(fn($get) => $get('insurance_status') === InsuranceStatus::DONE->value),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('insurance_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (InsuranceStatus $state): string => match ($state) {
                        InsuranceStatus::NOT_APPLICABLE => 'gray',
                        InsuranceStatus::PENDING => 'warning',
                        InsuranceStatus::DONE => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (InsuranceStatus $state) => $state->getLabel()),
                    
                Tables\Columns\TextColumn::make('insurance_number')
                    ->label('Insurance Number')
                    ->searchable()
                    ->placeholder('N/A')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('insurance_relation')
                    ->label('Relation')
                    ->formatStateUsing(fn (?InsuranceRelation $state) => $state?->getLabel() ?? 'N/A'),
                    
                Tables\Columns\TextColumn::make('annual_cost')
                    ->label('Annual Cost')
                    ->money('USD')
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('monthly_cost')
                    ->label('Monthly Cost')
                    ->money('USD')
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('activation_date')
                    ->label('Activation Date')
                    ->date()
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('deactivation_date')
                    ->label('Deactivation Date')
                    ->date()
                    ->placeholder('Active')
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('M d, Y') : 'Active'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('insurance_status')
                    ->options(InsuranceStatus::options())
                    ->label('Status'),
                    
                Tables\Filters\SelectFilter::make('insurance_relation')
                    ->options(InsuranceRelation::options())
                    ->label('Relation'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Medical Record')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Add Medical Record')
                    ->modalWidth('5xl'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalHeading('Edit Medical Record')
                        ->modalWidth('5xl'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No medical records found')
            ->emptyStateDescription('Add the first medical record for this employee.')
            ->emptyStateIcon('heroicon-o-heart')
            ->defaultSort('created_at', 'desc');
    }
}
