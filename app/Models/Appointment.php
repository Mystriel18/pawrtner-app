<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'pet_id',
    'client_user_id',
    'veterinarian_user_id',
    'scheduled_at',
    'duration_minutes',
    'status',
    'reason',
    'notes',
    'conflict_checked_at',
    'created_by',
    'updated_by',
])]
class Appointment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
    ];

    public const TRANSITIONS = [
        self::STATUS_PENDING => [
            self::STATUS_APPROVED,
            self::STATUS_DECLINED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_APPROVED => [
            self::STATUS_CANCELLED,
            self::STATUS_COMPLETED,
        ],
        self::STATUS_DECLINED => [],
        self::STATUS_CANCELLED => [],
        self::STATUS_COMPLETED => [],
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
            'conflict_checked_at' => 'datetime',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'veterinarian_user_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        $currentStatus = $this->status ?? self::STATUS_PENDING;

        return in_array($targetStatus, self::TRANSITIONS[$currentStatus] ?? [], true);
    }
}
