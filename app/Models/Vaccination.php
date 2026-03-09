<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = [
        'user_id',
        'pet_id',
        'pet_name',
        'owner_name',
        'contact_number',
        'vaccine_name',
        'date_given',
        'next_due_date'
    ];

    protected function casts(): array
    {
        return [
            'date_given' => 'date',
            'next_due_date' => 'date',
        ];
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        if ($user->isStaffOrAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayPetNameAttribute(): string
    {
        $manualPetName = trim((string) $this->pet_name);
        if ($manualPetName !== '') {
            return $manualPetName;
        }

        $linkedPetName = trim((string) $this->pet?->pet_name);

        return $linkedPetName !== '' ? $linkedPetName : 'N/A';
    }

    public function getDisplayOwnerNameAttribute(): string
    {
        $manualOwnerName = trim((string) $this->owner_name);
        if ($manualOwnerName !== '') {
            return $manualOwnerName;
        }

        $linkedOwnerName = trim((string) $this->pet?->owner_name);

        return $linkedOwnerName !== '' ? $linkedOwnerName : 'N/A';
    }

    public function getDisplayContactNumberAttribute(): string
    {
        $manualContactNumber = trim((string) $this->contact_number);
        if ($manualContactNumber !== '') {
            return $manualContactNumber;
        }

        $linkedContactNumber = trim((string) $this->pet?->contact_number);

        return $linkedContactNumber !== '' ? $linkedContactNumber : 'N/A';
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
