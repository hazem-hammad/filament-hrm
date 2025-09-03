<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Enum\InsuranceRelation;
use App\Enum\InsuranceStatus;
use App\Filament\Resources\MedicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListMedicalRecords extends ListRecords
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('insurance_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(InsuranceStatus $state): string => match ($state) {
                        InsuranceStatus::NOT_APPLICABLE => 'gray',
                        InsuranceStatus::PENDING => 'warning',
                        InsuranceStatus::DONE => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(InsuranceStatus $state) => $state->getLabel()),

                Tables\Columns\TextColumn::make('insurance_number')
                    ->label('Insurance Number')
                    ->searchable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('insurance_relation')
                    ->label('Relation')
                    ->formatStateUsing(fn(?InsuranceRelation $state) => $state?->getLabel() ?? 'N/A'),

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
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('insurance_status')
                    ->options(InsuranceStatus::options()),

                Tables\Filters\SelectFilter::make('insurance_relation')
                    ->options(InsuranceRelation::options()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
