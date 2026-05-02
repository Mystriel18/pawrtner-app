<?php

namespace App\Filament\Client\Resources\Appointments;

use App\Filament\Client\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Client\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Client\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Client\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Client\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
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
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query): void {
                $query
                    ->where('client_user_id', auth()->id())
                    ->orWhereHas('pet', function (Builder $petQuery): void {
                        $petQuery->where('owner_user_id', auth()->id());
                    });
            });
    }
}
