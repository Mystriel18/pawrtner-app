<?php

namespace App\Filament\Client\Resources\MedicalRecords;

use App\Filament\Client\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Filament\Client\Resources\MedicalRecords\Schemas\MedicalRecordForm;
use App\Filament\Client\Resources\MedicalRecords\Tables\MedicalRecordsTable;
use App\Models\MedicalRecord;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return MedicalRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalRecordsTable::configure($table);
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
            'index' => ListMedicalRecords::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('pet', fn (Builder $query): Builder => $query->where('owner_user_id', auth()->id()));
    }

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
