<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'pet_id',
    'veterinarian_user_id',
    'appointment_id',
    'visit_date',
    'chief_complaint',
    'diagnosis',
    'treatment',
    'prescription',
    'weight_kg',
    'temperature_c',
    'notes',
    'created_by',
    'updated_by',
])]
class MedicalRecord extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'weight_kg' => 'decimal:2',
            'temperature_c' => 'decimal:1',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_user_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class);
    }
}
