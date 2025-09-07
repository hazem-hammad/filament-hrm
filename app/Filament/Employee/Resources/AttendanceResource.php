<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Attendance';

    protected static ?string $modelLabel = 'Attendance';

    protected static ?string $pluralModelLabel = 'Attendance Records';

    // Employees cannot create/edit attendance records
    public static function form(Form $form): Form
    {
        return $form->schema([]);
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
        ];
    }

    // Filter to show only current employee's attendance
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('employee_id', auth()->id())
            ->with(['workPlan']);
    }

    // Disable create and edit for employees
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
