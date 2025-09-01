<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Enum\AssetCondition;
use App\Enum\AssetStatus;
use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['assignedEmployee']))
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->label('Image')
                    ->collection('images')
                    ->conversion('thumb')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/asset-placeholder.png'),

                Tables\Columns\TextColumn::make('asset_id')
                    ->label('Asset ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Asset ID copied')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn(Asset $record): ?string => $record->brand && $record->model ? "{$record->brand} {$record->model}" : null),

                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn(AssetCondition $state): string => $state->color())
                    ->formatStateUsing(fn(AssetCondition $state): string => $state->label()),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(AssetStatus $state): string => $state->color())
                    ->formatStateUsing(fn(AssetStatus $state): string => $state->label()),

                Tables\Columns\TextColumn::make('assignedEmployee.name')
                    ->label('Assigned To')
                    ->placeholder('Not Assigned')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('No location'),

                Tables\Columns\TextColumn::make('purchase_cost')
                    ->label('Cost')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Purchased')
                    ->date()
                    ->sortable()
                    ->description(fn(Asset $record): ?string => $record->purchase_date?->diffForHumans())
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_under_warranty')
                    ->label('Warranty')
                    ->getStateUsing(fn(Asset $record): bool => $record->isUnderWarranty())
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn(Asset $record): string => $record->warranty_expires_at ? "Expires: {$record->warranty_expires_at->format('M d, Y')}" : 'No warranty info')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Computer Equipment' => 'Computer Equipment',
                        'Office Equipment' => 'Office Equipment',
                        'Furniture' => 'Furniture',
                        'Vehicle' => 'Vehicle',
                        'Software' => 'Software',
                        'Mobile Device' => 'Mobile Device',
                        'Network Equipment' => 'Network Equipment',
                        'Other' => 'Other',
                    ])
                    ->searchable()
                    ->native(false)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->options(AssetStatus::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('condition')
                    ->options(AssetCondition::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned Employee')
                    ->relationship('assignedEmployee', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('purchase_date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('purchased_from')
                            ->label('Purchased From'),
                        \Filament\Forms\Components\DatePicker::make('purchased_until')
                            ->label('Purchased Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchased_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '>=', $date)
                            )
                            ->when(
                                $data['purchased_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '<=', $date)
                            );
                    }),

                Tables\Filters\Filter::make('warranty_status')
                    ->label('Warranty Status')
                    ->form([
                        \Filament\Forms\Components\Select::make('warranty_status')
                            ->options([
                                'active' => 'Under Warranty',
                                'expired' => 'Warranty Expired',
                                'no_warranty' => 'No Warranty Info',
                            ])
                            ->placeholder('Select warranty status'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['warranty_status'] ?? null, function (Builder $query, $status) {
                            match ($status) {
                                'active' => $query->where('warranty_expires_at', '>', now()),
                                'expired' => $query->where('warranty_expires_at', '<=', now()),
                                'no_warranty' => $query->whereNull('warranty_expires_at'),
                            };
                        });
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('assign')
                        ->label('Assign')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->visible(fn(Asset $record): bool => $record->status === AssetStatus::AVAILABLE && $record->is_active)
                        ->form([
                            \Filament\Forms\Components\Select::make('employee_id')
                                ->label('Assign to Employee')
                                ->relationship('assignedEmployee', 'name', fn($query) => $query->active())
                                ->searchable()
                                ->preload()
                                ->required(),
                            \Filament\Forms\Components\DatePicker::make('assigned_at')
                                ->label('Assignment Date')
                                ->default(today())
                                ->required(),
                        ])
                        ->action(function (Asset $record, array $data) {
                            $record->update([
                                'assigned_to' => $data['employee_id'],
                                'assigned_at' => $data['assigned_at'],
                                'status' => AssetStatus::ASSIGNED,
                            ]);
                        }),
                    Tables\Actions\Action::make('unassign')
                        ->label('Unassign')
                        ->icon('heroicon-o-user-minus')
                        ->color('warning')
                        ->visible(fn(Asset $record): bool => $record->status === AssetStatus::ASSIGNED)
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to unassign this asset?')
                        ->action(function (Asset $record) {
                            $record->update([
                                'assigned_to' => null,
                                'assigned_at' => null,
                                'status' => AssetStatus::AVAILABLE,
                            ]);
                        }),
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn(Asset $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn(Asset $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(Asset $record): string => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(Asset $record): string =>
                            $record->is_active
                                ? 'Are you sure you want to deactivate this asset?'
                                : 'Are you sure you want to activate this asset?'
                        )
                        ->action(fn(Asset $record) => $record->update(['is_active' => !$record->is_active])),
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
                        ->modalDescription('Are you sure you want to activate the selected assets?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['is_active' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to deactivate the selected assets?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['is_active' => false]));
                        }),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                $csvData = "Asset ID,Name,Category,Brand,Model,Serial Number,Status,Condition,Assigned To,Location,Purchase Cost,Purchase Date\n";
                                foreach ($records as $record) {
                                    $csvData .= sprintf(
                                        "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                                        $record->asset_id,
                                        $record->name,
                                        $record->category,
                                        $record->brand ?? '',
                                        $record->model ?? '',
                                        $record->serial_number ?? '',
                                        $record->status->label(),
                                        $record->condition->label(),
                                        $record->assignedEmployee?->name ?? '',
                                        $record->location ?? '',
                                        $record->purchase_cost ?? '',
                                        $record->purchase_date?->format('Y-m-d') ?? ''
                                    );
                                }
                                echo $csvData;
                            }, 'assets-export-' . now()->format('Y-m-d-H-i-s') . '.csv');
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No assets found')
            ->emptyStateDescription('Get started by adding your first asset.')
            ->emptyStateIcon('heroicon-o-computer-desktop')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Asset')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
