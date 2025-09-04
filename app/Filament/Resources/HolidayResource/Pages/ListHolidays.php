<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Models\Holiday;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('import_holidays')
            //     ->label('Import Holidays')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->color('info')
            //     ->form([
            //         Forms\Components\FileUpload::make('file')
            //             ->label('CSV File')
            //             ->acceptedFileTypes(['text/csv', 'application/csv'])
            //             ->required()
            //             ->helperText('Upload a CSV file with columns: name, description, start_date, end_date, type, is_paid'),
            //     ])
            //     ->action(function (array $data) {
            //         $this->importHolidays($data['file']);
            //     }),

            // Action::make('export_holidays')
            //     ->label('Export Holidays')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('success')
            //     ->action(function () {
            //         return $this->exportHolidays();
            //     }),

            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function importHolidays($filePath): void
    {
        try {
            $path = storage_path('app/' . $filePath);
            $csvData = array_map('str_getcsv', file($path));
            $header = array_shift($csvData);

            $imported = 0;
            foreach ($csvData as $row) {
                $data = array_combine($header, $row);

                Holiday::create([
                    'name' => $data['name'] ?? '',
                    'description' => $data['description'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'type' => in_array($data['type'] ?? '', ['public', 'religious', 'national', 'company'])
                        ? $data['type'] : 'public',
                    'is_paid' => filter_var($data['is_paid'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'status' => true,
                    'color' => $data['color'] ?? '#3B82F6',
                ]);
                $imported++;
            }

            Notification::make()
                ->title('Import Successful')
                ->body("Imported {$imported} holidays successfully.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body('There was an error importing the holidays: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function exportHolidays()
    {
        $holidays = Holiday::all();

        $csvData = "name,description,start_date,end_date,type,is_paid,is_recurring,color,status\n";

        foreach ($holidays as $holiday) {
            $csvData .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $holiday->name,
                $holiday->description ?? '',
                $holiday->start_date->format('Y-m-d'),
                $holiday->end_date->format('Y-m-d'),
                $holiday->type,
                $holiday->is_paid ? 'true' : 'false',
                $holiday->is_recurring ? 'true' : 'false',
                $holiday->color,
                $holiday->status ? 'active' : 'inactive'
            );
        }

        return response()->streamDownload(
            function () use ($csvData) {
                echo $csvData;
            },
            'holidays-export-' . now()->format('Y-m-d-H-i-s') . '.csv',
            ['Content-Type' => 'text/csv']
        );
    }
}
