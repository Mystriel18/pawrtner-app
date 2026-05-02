<?php

namespace App\Filament\Vet\Resources\Vaccinations;

use App\Filament\Vet\Resources\Vaccinations\Pages\CreateVaccination;
use App\Filament\Vet\Resources\Vaccinations\Pages\EditVaccination;
use App\Filament\Vet\Resources\Vaccinations\Pages\ListVaccinations;
use App\Filament\Vet\Resources\Vaccinations\Schemas\VaccinationForm;
use App\Filament\Vet\Resources\Vaccinations\Tables\VaccinationsTable;
use App\Models\Vaccination;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VaccinationResource extends Resource
{
    protected static ?string $model = Vaccination::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function form(Schema $schema): Schema
    {
        return VaccinationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VaccinationsTable::configure($table);
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
            'index' => ListVaccinations::route('/'),
            'create' => CreateVaccination::route('/create'),
            'edit' => EditVaccination::route('/{record}/edit'),
        ];
    }
}
