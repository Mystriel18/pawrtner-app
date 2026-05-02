<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pet_id',
    'medical_record_id',
    'administered_by_user_id',
    'vaccine_name',
    'manufacturer',
    'batch_number',
    'administered_at',
    'next_due_at',
    'status',
    'notes',
    'created_by',
    'updated_by',
])]
class Vaccination extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
            'next_due_at' => 'date',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by_user_id');
    }
}
