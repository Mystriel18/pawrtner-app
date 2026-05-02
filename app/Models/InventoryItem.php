<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'sku',
    'name',
    'category',
    'unit',
    'quantity_on_hand',
    'reorder_level',
    'cost_amount',
    'price_amount',
    'expires_at',
    'is_active',
    'notes',
    'created_by',
    'updated_by',
])]
class InventoryItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'cost_amount' => 'decimal:2',
            'price_amount' => 'decimal:2',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
