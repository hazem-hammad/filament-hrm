<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Enum\EmployeeLevel;
use App\Enum\MaritalStatus;
use App\Enum\ContractType;
use App\Enum\SocialInsuranceStatus;
use App\Filament\Resources\EmployeeResource;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['department', 'position', 'manager', 'workPlans']))
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('profile')
                    ->label('Photo')
                    ->collection('profile')
                    ->conversion('thumb')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(
                        fn(Employee $record): string =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'
                    ),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Employee ID copied')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->label('National ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marital_status')
                    ->label('Marital Status')
                    ->formatStateUsing(fn(MaritalStatus $state): string => $state->label())
                    ->badge()
                    ->color(fn(MaritalStatus $state): string => match($state) {
                        MaritalStatus::SINGLE => 'gray',
                        MaritalStatus::MARRIED => 'success',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Employee $record): string => $record->level->label()),
                Tables\Columns\TextColumn::make('contract_type')
                    ->label('Contract Type')
                    ->formatStateUsing(fn(ContractType $state): string => $state->label())
                    ->badge()
                    ->color(fn(ContractType $state): string => match($state) {
                        ContractType::PERMANENT => 'success',
                        ContractType::FULLTIME => 'info',
                        ContractType::PARTTIME => 'warning',
                        ContractType::FREELANCE => 'gray',
                        ContractType::CREDIT_HOURS => 'purple',
                        ContractType::INTERNSHIP => 'orange',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('social_insurance_status')
                    ->label('Social Insurance')
                    ->formatStateUsing(fn(SocialInsuranceStatus $state): string => $state->getLabel())
                    ->badge()
                    ->color(fn(SocialInsuranceStatus $state): string => match($state) {
                        SocialInsuranceStatus::NOT_APPLICABLE => 'gray',
                        SocialInsuranceStatus::PENDING => 'warning',
                        SocialInsuranceStatus::DONE => 'success',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('social_insurance_number')
                    ->label('Insurance Number')
                    ->placeholder('Not Provided')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Reports To')
                    ->placeholder('No Manager')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_date_of_joining')
                    ->label('Joining Date')
                    ->date()
                    ->sortable()
                    ->description(fn(Employee $record): string => $record->company_date_of_joining->diffForHumans()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('level')
                    ->label('Level')
                    ->options(EmployeeLevel::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('social_insurance_status')
                    ->label('Social Insurance Status')
                    ->options(SocialInsuranceStatus::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('reporting_to')
                    ->label('Reports To')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('work_plans')
                    ->label('Work Plan')
                    ->relationship('workPlans', 'name', fn($query) => $query->active())
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\Filter::make('joining_date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('joined_from')
                            ->label('Joined From'),
                        \Filament\Forms\Components\DatePicker::make('joined_until')
                            ->label('Joined Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['joined_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('company_date_of_joining', '>=', $date)
                            )
                            ->when(
                                $data['joined_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('company_date_of_joining', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['joined_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Joined from ' . \Carbon\Carbon::parse($data['joined_from'])->toFormattedDateString())
                                ->removeField('joined_from');
                        }
                        if ($data['joined_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Joined until ' . \Carbon\Carbon::parse($data['joined_until'])->toFormattedDateString())
                                ->removeField('joined_until');
                        }
                        return $indicators;
                    }),
                Tables\Filters\Filter::make('age_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('age_from')
                            ->label('Age From')
                            ->numeric()
                            ->minValue(18)
                            ->maxValue(100),
                        \Filament\Forms\Components\TextInput::make('age_to')
                            ->label('Age To')
                            ->numeric()
                            ->minValue(18)
                            ->maxValue(100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['age_from'] ?? null, function (Builder $query, $age): Builder {
                                return $query->whereDate('date_of_birth', '<=', now()->subYears($age));
                            })
                            ->when($data['age_to'] ?? null, function (Builder $query, $age): Builder {
                                return $query->whereDate('date_of_birth', '>=', now()->subYears($age + 1));
                            });
                    }),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('marital_status')
                    ->label('Marital Status')
                    ->options(MaritalStatus::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('Contract Type')
                    ->options(ContractType::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(Employee $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(Employee $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(Employee $record): string => ($record->status ?? false) ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(Employee $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this employee?'
                                : 'Are you sure you want to activate this employee?'
                        )
                        ->action(fn(Employee $record) => $record->update(['status' => !$record->status])),
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
                        ->modalDescription('Are you sure you want to activate the selected employees?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['status' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to deactivate the selected employees?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['status' => false]));
                        }),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                $csvData = "Name,Employee ID,Email,Phone,Department,Position,Level,Status,Joining Date\n";
                                foreach ($records as $record) {
                                    $csvData .= sprintf(
                                        "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                                        $record->name,
                                        $record->employee_id,
                                        $record->email,
                                        $record->phone,
                                        $record->department?->name ?? '',
                                        $record->position?->name ?? '',
                                        $record->level?->label() ?? '',
                                        $record->status ? 'Active' : 'Inactive',
                                        $record->company_date_of_joining->format('Y-m-d')
                                    );
                                }
                                echo $csvData;
                            }, 'employees-export-' . now()->format('Y-m-d-H-i-s') . '.csv');
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No employees found')
            ->emptyStateDescription('Get started by creating your first employee.')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Employee')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
            Actions\Action::make('import')
                ->label('Import Employees')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Section::make('Import Instructions')
                        ->description('Follow these steps to import employees successfully')
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('instructions')
                                ->content(new \Illuminate\Support\HtmlString('
                                    <div class="space-y-4">
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                            <p class="text-sm text-blue-800">
                                                <strong>💡 Tip:</strong> Download the template below for proper formatting and sample data.
                                            </p>
                                        </div>
                                    </div>
                                '))
                                ->columnSpanFull(),
                            \Filament\Forms\Components\Actions::make([
                                \Filament\Forms\Components\Actions\Action::make('download_template')
                                    ->label('Download Template')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('success')
                                    ->action(function () {
                                        return response()->download(
                                            public_path('samples/imports/employee_import_template.xlsx'),
                                            'employee_import_template.xlsx'
                                        );
                                    })
                            ])->columnSpanFull(),
                        ])->collapsible(),
                    FileUpload::make('file')
                        ->label('Excel File')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                            'application/csv'
                        ])
                        ->required()
                        ->helperText('Upload an Excel file (.xlsx, .xls) or CSV file with employee data')
                        ->columnSpanFull()
                ])
                ->action(function (array $data): void {
                    // Get the uploaded file path
                    $uploadedFile = $data['file'];
                    
                    // Try multiple possible paths
                    $possiblePaths = [
                        storage_path('app/' . $uploadedFile),                    // storage/app/imports/file.xlsx
                        storage_path('app/public/' . $uploadedFile),             // storage/app/public/imports/file.xlsx
                        storage_path('app/livewire-tmp/' . $uploadedFile),       // Livewire temp directory
                        storage_path('app/livewire-tmp/' . basename($uploadedFile)), // Just filename in livewire-tmp
                        $uploadedFile,                                           // Direct path if absolute
                    ];
                    
                    $filePath = null;
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $filePath = $path;
                            break;
                        }
                    }
                    
                    if (!$filePath) {
                        // Debug: List what's actually in the directories
                        $debugInfo = [];
                        $debugDirs = [
                            'storage/app' => storage_path('app'),
                            'storage/app/imports' => storage_path('app/imports'),
                            'storage/app/public' => storage_path('app/public'),
                            'storage/app/livewire-tmp' => storage_path('app/livewire-tmp'),
                        ];
                        
                        foreach ($debugDirs as $label => $dir) {
                            if (is_dir($dir)) {
                                $files = array_slice(scandir($dir), 2, 5); // Skip . and .., take first 5
                                $debugInfo[] = $label . ': ' . implode(', ', $files);
                            }
                        }
                        
                        Notification::make()
                            ->title('Import Failed')
                            ->body('File not found. Searched: ' . $uploadedFile . '. Found in dirs: ' . implode(' | ', $debugInfo))
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $import = new EmployeeImport();
                        Excel::import($import, $filePath);

                        $importedCount = $import->getImportedCount();
                        $errors = $import->getErrors();

                        if ($importedCount > 0) {
                            Notification::make()
                                ->title('Import Successful')
                                ->body("Successfully imported {$importedCount} employees. Welcome emails will be sent shortly.")
                                ->success()
                                ->send();
                        }

                        if (!empty($errors)) {
                            Notification::make()
                                ->title('Import Completed with Errors')
                                ->body('Some rows had errors: ' . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '... and ' . (count($errors) - 3) . ' more' : ''))
                                ->warning()
                                ->send();
                        }
                        
                        if ($importedCount === 0 && empty($errors)) {
                            Notification::make()
                                ->title('Import Warning')
                                ->body('No data was found in the uploaded file. Please check the file format and ensure it contains employee data.')
                                ->warning()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        // Clean up the uploaded file
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                })
        ];
    }
}
