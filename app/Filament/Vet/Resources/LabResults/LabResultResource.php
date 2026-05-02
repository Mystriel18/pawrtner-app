<?php

namespace App\Filament\Vet\Resources\LabResults;

use App\Filament\Vet\Resources\LabResults\Pages\CreateLabResult;
use App\Filament\Vet\Resources\LabResults\Pages\EditLabResult;
use App\Filament\Vet\Resources\LabResults\Pages\ListLabResults;
use App\Filament\Vet\Resources\LabResults\Schemas\LabResultForm;
use App\Filament\Vet\Resources\LabResults\Tables\LabResultsTable;
use App\Models\LabResult;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LabResultResource extends Resource
{
    protected static ?string $model = LabResult::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    public static function form(Schema $schema): Schema
    {
        return LabResultForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LabResultsTable::configure($table);
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
            'index' => ListLabResults::route('/'),
            'create' => CreateLabResult::route('/create'),
            'edit' => EditLabResult::route('/{record}/edit'),
        ];
    }
}
