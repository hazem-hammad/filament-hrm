<?php

namespace App\Filament\Resources\DocumentFolderResource\Pages;

use App\Filament\Resources\DocumentFolderResource;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Employee;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class ViewDocumentFolder extends ViewRecord
{

    protected static string $resource = DocumentFolderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Folder Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Folder Name')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->icon('heroicon-o-folder')
                                    ->iconColor(fn($record): string => $record->color),

                                TextEntry::make('parent.name')
                                    ->label('Parent Folder')
                                    ->placeholder('Root Directory')
                                    ->icon('heroicon-o-folder-open'),

                                TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('No description')
                                    ->columnSpanFull(),

                                IconEntry::make('is_private')
                                    ->label('Privacy')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-lock-closed')
                                    ->falseIcon('heroicon-o-globe-alt')
                                    ->trueColor('danger')
                                    ->falseColor('success'),

                                TextEntry::make('creator.name')
                                    ->label('Created By')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('item_count')
                                    ->label('Total Items')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('formatted_size')
                                    ->label('Total Size')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createSubfolder')
                ->label('New Subfolder')
                ->icon('heroicon-o-folder-plus')
                ->color('primary')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, \Filament\Forms\Set $set) {
                            $set('name', str($state)->title());
                        }),

                    Textarea::make('description')
                        ->maxLength(1000)
                        ->rows(3),

                    ColorPicker::make('color')
                        ->default('#1976D2')
                        ->required(),

                    Toggle::make('is_private')
                        ->label('Private Folder')
                        ->default(false),

                    Hidden::make('parent_id')
                        ->default(fn() => $this->record->id),

                    Hidden::make('created_by')
                        ->default(fn() => Auth::guard('admin')->id()),
                ])
                ->action(function (array $data): void {
                    DocumentFolder::create($data);
                    $this->refreshFormData(['subfolders', 'documents']);
                }),

            Action::make('uploadDocument')
                ->label('Upload File')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->required()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain',
                            'image/*',
                            'video/*',
                            'audio/*',
                            'application/zip',
                        ])
                        ->maxSize(50 * 1024)
                        ->directory('documents')
                        ->storeFileNamesIn('original_filename'),

                    TextInput::make('name')
                        ->label('Document Name')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->maxLength(1000)
                        ->rows(3),

                    Select::make('assigned_to')
                        ->label('Assign to Employee')
                        ->options(Employee::query()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Toggle::make('is_private')
                        ->label('Private Document')
                        ->default(false),

                    Hidden::make('folder_id')
                        ->default(fn() => $this->record->id),

                    Hidden::make('created_by')
                        ->default(fn() => Auth::guard('admin')->id()),
                ])
                ->modalWidth(MaxWidth::Large)
                ->action(function (array $data): void {
                    $filePath = $data['file'];
                    unset($data['file']);

                    // Get file info from uploaded file
                    if ($filePath) {
                        $filename = basename($filePath);
                        $fileInfo = pathinfo($filename);

                        // Set default name if not provided
                        if (!$data['name']) {
                            $data['name'] = $fileInfo['filename'];
                        }

                        // Set required file metadata before creating
                        $data['original_filename'] = $filename;
                        $data['file_type'] = strtolower($fileInfo['extension'] ?? 'unknown');
                        $data['mime_type'] = mime_content_type(storage_path('app/public/' . $filePath)) ?: 'application/octet-stream';
                        $data['file_size'] = filesize(storage_path('app/public/' . $filePath)) ?: 0;
                    } else {
                        // Set defaults for required fields if no file
                        $data['file_type'] = 'unknown';
                        $data['mime_type'] = 'application/octet-stream';
                        $data['file_size'] = 0;
                    }

                    $document = Document::create($data);

                    // Store the uploaded file using Spatie Media Library
                    if ($filePath) {
                        $document->addMediaFromDisk($filePath, 'public')
                            ->toMediaCollection('documents');
                    }

                    $this->refreshFormData(['subfolders', 'documents']);
                }),

            Actions\EditAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'subfolders' => $this->record->children()->with('creator')->get(),
            'documents' => $this->record->documents()->with(['assignedEmployee', 'creator'])->get(),
        ];
    }

    public function deleteSubfolder(int $folderId): void
    {
        $folder = DocumentFolder::findOrFail($folderId);
        $folder->delete();
        $this->refreshFormData(['subfolders', 'documents']);

        \Filament\Notifications\Notification::make()
            ->title('Folder deleted successfully')
            ->success()
            ->send();
    }

    public function deleteDocument(int $documentId): void
    {
        $document = Document::findOrFail($documentId);
        $document->delete();
        $this->refreshFormData(['subfolders', 'documents']);

        \Filament\Notifications\Notification::make()
            ->title('Document deleted successfully')
            ->success()
            ->send();
    }
}
