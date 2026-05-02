<?php

namespace App\Filament\Resources\FinancialRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinancialRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment.id')
                    ->label('Appointment')
                    ->searchable(),
                TextColumn::make('pet.name')
                    ->label('Pet')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable(),
                TextColumn::make('recordedBy.name')
                    ->label('Recorded By')
                    ->sortable(),
                TextColumn::make('entry_type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('occurred_on')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('entry_type')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'posted' => 'Posted',
                        'pending' => 'Pending',
                        'void' => 'Void',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
