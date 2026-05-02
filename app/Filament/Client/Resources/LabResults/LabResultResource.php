<?php

namespace App\Filament\Client\Resources\LabResults;

use App\Filament\Client\Resources\LabResults\Pages\ListLabResults;
use App\Filament\Client\Resources\LabResults\Schemas\LabResultForm;
use App\Filament\Client\Resources\LabResults\Tables\LabResultsTable;
use App\Models\LabResult;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
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
