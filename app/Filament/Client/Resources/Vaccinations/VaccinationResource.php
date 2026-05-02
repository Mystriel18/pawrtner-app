<?php

namespace App\Filament\Client\Resources\Vaccinations;

use App\Filament\Client\Resources\Vaccinations\Pages\ListVaccinations;
use App\Filament\Client\Resources\Vaccinations\Schemas\VaccinationForm;
use App\Filament\Client\Resources\Vaccinations\Tables\VaccinationsTable;
use App\Models\Vaccination;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
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
