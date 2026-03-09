<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class Announcement extends Model
{
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_IMPORTANT = 'important';

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'is_pinned',
        'priority',
        'publish_at',
        'expires_at',
        'created_by',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'publish_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleOnWelcome(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();
        $currentTime = now(config('app.timezone'));

        if (! Schema::hasColumn($table, 'publish_at') || ! Schema::hasColumn($table, 'expires_at')) {
            return $query;
        }

        return $query
            ->where('publish_at', '<=', $currentTime)
            ->where(function (Builder $innerQuery) use ($currentTime): void {
                $innerQuery
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $currentTime);
            });
    }

    public function scopeOrderedForWelcome(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        if (! Schema::hasColumn($table, 'is_pinned')) {
            return $query->latest();
        }

        if (! Schema::hasColumn($table, 'publish_at')) {
            return $query
                ->orderByDesc('is_pinned')
                ->latest();
        }

        return $query
            ->orderByDesc('is_pinned')
            ->orderByRaw('COALESCE(publish_at, created_at) DESC')
            ->latest();
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->priority === self::PRIORITY_IMPORTANT ? 'IMPORTANT' : 'ANNOUNCEMENT';
    }
}
