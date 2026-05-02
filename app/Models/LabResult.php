<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pet_id',
    'medical_record_id',
    'uploaded_by_user_id',
    'test_name',
    'result_summary',
    'result_date',
    'file_path',
    'file_name',
    'mime_type',
    'file_size_bytes',
    'created_by',
    'updated_by',
])]
class LabResult extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'result_date' => 'date',
            'file_size_bytes' => 'integer',
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

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
