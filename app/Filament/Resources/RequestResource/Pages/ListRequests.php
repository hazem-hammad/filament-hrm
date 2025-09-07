<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['employee', 'requestable', 'approver']))
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(Request $record): string => $record->request_type_color)
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('requestable.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacation_period')
                    ->label('Period')
                    ->getStateUsing(function (Request $record) {
                        if ($record->isVacation()) {
                            return $record->start_date->format('M d') . ' - ' . $record->end_date->format('M d, Y') . ' (' . $record->total_days . ' days)';
                        } else {
                            return $record->request_date->format('M d, Y') . ' (' . $record->hours . 'h)';
                        }
                    })
                    ->searchable(['start_date', 'end_date', 'request_date']),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(Request $record): string => $record->status_color)
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('Pending')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Request::STATUSES)
                    ->multiple(),
                SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From'),
                        DatePicker::make('created_until')
                            ->label('Until'),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to approve this request?')
                        ->action(function (Request $record) {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => null, // Set to null when approved by admin
                                'approved_at' => now(),
                            ]);
                        })
                        ->visible(fn(Request $record) => $record->canBeApproved()),
                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Rejection Reason')
                                ->required()
                                ->placeholder('Please provide a reason for rejection...'),
                        ])
                        ->action(function (Request $record, array $data) {
                            $record->update([
                                'status' => 'rejected',
                                'admin_notes' => $data['admin_notes'],
                                'approved_by' => null, // Set to null when rejected by admin
                                'approved_at' => now(),
                            ]);
                        })
                        ->visible(fn(Request $record) => $record->canBeRejected()),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests')
                ->badge(Request::query()->count()),
            'vacation' => Tab::make('Vacation')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('request_type', 'vacation'))
                ->badge(Request::where('request_type', 'vacation')->count()),
            'attendance' => Tab::make('Attendance')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('request_type', 'attendance'))
                ->badge(Request::where('request_type', 'attendance')->count()),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(Request::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved'))
                ->badge(Request::where('status', 'approved')->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badge(Request::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
