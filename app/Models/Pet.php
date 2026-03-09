<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'user_id',
        'owner_name',
        'contact_number',
        'pet_name',
        'species',
        'breed',
        'sex',
        'age',
        'age_value',
        'age_type',
    ];

    protected $casts = [
        'age' => 'integer',
        'age_value' => 'integer',
    ];

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        if ($user->isStaffOrAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function user(): BelongsTo
    {
        return $this->owner();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function hospitalizations(): HasMany
    {
        return $this->hasMany(Hospitalization::class);
    }

    public function getDisplayAgeAttribute(): ?string
    {
        $value = $this->age_value ?? $this->age;

        if ($value === null) {
            return null;
        }

        $type = $this->age_type === 'month' ? 'month' : 'year';

        return $value.' '.$type.($value === 1 ? '' : 's');
    }
}
