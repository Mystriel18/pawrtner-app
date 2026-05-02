<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Validation\ValidationException;

class InventoryMovementService
{
    public function recordInitialStock(InventoryItem $item, ?int $actorUserId = null): ?InventoryMovement
    {
        $quantity = (float) $item->quantity_on_hand;

        if ($quantity <= 0) {
            return null;
        }

        return InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'moved_by_user_id' => $actorUserId,
            'movement_type' => 'initial',
            'quantity' => $quantity,
            'quantity_before' => 0,
            'quantity_after' => $quantity,
            'reason' => 'Initial stock recorded on item creation.',
            'moved_at' => now(),
            'created_by' => $actorUserId,
            'updated_by' => $actorUserId,
        ]);
    }

    public function adjustStock(
        InventoryItem $item,
        string $movementType,
        float $quantity,
        ?int $actorUserId = null,
        ?string $reason = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): InventoryMovement {
        if ($quantity == 0.0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be non-zero.',
            ]);
        }

        $item->refresh();

        $before = (float) $item->quantity_on_hand;
        $delta = $this->resolveDelta($movementType, $quantity);
        $after = $before + $delta;

        if ($after < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Quantity on hand cannot go below zero.',
            ]);
        }

        $item->forceFill([
            'quantity_on_hand' => $after,
            'updated_by' => $actorUserId,
        ])->save();

        return InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'moved_by_user_id' => $actorUserId,
            'movement_type' => $movementType,
            'quantity' => abs($quantity),
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reason' => $reason,
            'moved_at' => now(),
            'created_by' => $actorUserId,
            'updated_by' => $actorUserId,
        ]);
    }

    private function resolveDelta(string $movementType, float $quantity): float
    {
        return match ($movementType) {
            'stock_in' => abs($quantity),
            'stock_out' => -abs($quantity),
            'adjustment' => $quantity,
            default => throw ValidationException::withMessages([
                'movement_type' => 'Invalid movement type.',
            ]),
        };
    }
}
