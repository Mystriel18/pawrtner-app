<?php

namespace App\Filament\Vet\Resources\MedicalRecords;

use App\Filament\Vet\Resources\MedicalRecords\Pages\CreateMedicalRecord;
use App\Filament\Vet\Resources\MedicalRecords\Pages\EditMedicalRecord;
use App\Filament\Vet\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Filament\Vet\Resources\MedicalRecords\Schemas\MedicalRecordForm;
use App\Filament\Vet\Resources\MedicalRecords\Tables\MedicalRecordsTable;
use App\Models\MedicalRecord;
use BackedEnum;
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
            'create' => CreateMedicalRecord::route('/create'),
            'edit' => EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
