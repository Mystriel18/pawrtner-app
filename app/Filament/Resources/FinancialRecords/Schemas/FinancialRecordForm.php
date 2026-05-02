<?php

namespace App\Filament\Resources\FinancialRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FinancialRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('appointment_id')
                    ->relationship('appointment', 'id')
                    ->searchable()
                    ->preload(),
                Select::make('pet_id')
                    ->relationship('pet', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('client_user_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('recorded_by_user_id')
                    ->relationship('recordedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn (): ?int => auth()->id()),
                Select::make('entry_type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                    ])
                    ->required(),
                TextInput::make('category')
                    ->maxLength(255),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('currency')
                    ->required()
                    ->maxLength(3)
                    ->default('PHP'),
                DatePicker::make('occurred_on')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->options([
                        'posted' => 'Posted',
                        'pending' => 'Pending',
                        'void' => 'Void',
                    ])
                    ->required()
                    ->default('posted'),
                Textarea::make('description')
                    ->columnSpanFull(),
                KeyValue::make('meta')
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(fn (): ?int => auth()->id()),
                Hidden::make('updated_by')
                    ->default(fn (): ?int => auth()->id()),
            ]);
    }
}
