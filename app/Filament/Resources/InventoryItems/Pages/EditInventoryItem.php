<?php

namespace App\Filament\Resources\InventoryItems\Pages;

use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Services\InventoryMovementService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditInventoryItem extends EditRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('stockIn')
                ->label('Stock In')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    TextInput::make('quantity')
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Textarea::make('reason')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    app(InventoryMovementService::class)->adjustStock(
                        item: $this->record,
                        movementType: 'stock_in',
                        quantity: (float) $data['quantity'],
                        actorUserId: Auth::id(),
                        reason: $data['reason'] ?? 'Stock in from admin adjustment action.',
                    );

                    $this->record->refresh();
                    $this->fillForm();

                    Notification::make()
                        ->title('Stock updated')
                        ->success()
                        ->send();
                }),
            Action::make('stockOut')
                ->label('Stock Out')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger')
                ->form([
                    TextInput::make('quantity')
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Textarea::make('reason')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    app(InventoryMovementService::class)->adjustStock(
                        item: $this->record,
                        movementType: 'stock_out',
                        quantity: (float) $data['quantity'],
                        actorUserId: Auth::id(),
                        reason: $data['reason'] ?? 'Stock out from admin adjustment action.',
                    );

                    $this->record->refresh();
                    $this->fillForm();

                    Notification::make()
                        ->title('Stock updated')
                        ->success()
                        ->send();
                }),
            Action::make('adjustment')
                ->label('Adjust Stock')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('warning')
                ->form([
                    TextInput::make('quantity')
                        ->label('Adjustment quantity (+ / -)')
                        ->numeric()
                        ->required(),
                    Textarea::make('reason')
                        ->maxLength(255)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(InventoryMovementService::class)->adjustStock(
                        item: $this->record,
                        movementType: 'adjustment',
                        quantity: (float) $data['quantity'],
                        actorUserId: Auth::id(),
                        reason: $data['reason'],
                    );

                    $this->record->refresh();
                    $this->fillForm();

                    Notification::make()
                        ->title('Stock adjusted')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $currentQuantity = (float) $record->quantity_on_hand;
        $requestedQuantity = array_key_exists('quantity_on_hand', $data)
            ? (float) $data['quantity_on_hand']
            : $currentQuantity;

        unset($data['quantity_on_hand']);

        $record->update($data);

        $delta = $requestedQuantity - $currentQuantity;

        if (abs($delta) > 0.000001) {
            app(InventoryMovementService::class)->adjustStock(
                item: $record,
                movementType: 'adjustment',
                quantity: $delta,
                actorUserId: Auth::id(),
                reason: 'Quantity updated from inventory item edit form.',
            );
        }

        return $record->refresh();
    }
}
