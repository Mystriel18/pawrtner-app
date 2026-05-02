<?php

namespace App\Filament\Client\Resources\Pets;

use App\Filament\Client\Resources\Pets\Pages\CreatePet;
use App\Filament\Client\Resources\Pets\Pages\EditPet;
use App\Filament\Client\Resources\Pets\Pages\ListPets;
use App\Filament\Client\Resources\Pets\Schemas\PetForm;
use App\Filament\Client\Resources\Pets\Tables\PetsTable;
use App\Models\Pet;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    public static function form(Schema $schema): Schema
    {
        return PetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetsTable::configure($table);
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
            'index' => ListPets::route('/'),
            'create' => CreatePet::route('/create'),
            'edit' => EditPet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('owner_user_id', auth()->id());
    }
}
