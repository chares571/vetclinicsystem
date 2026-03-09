<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'stock_quantity',
        'expiration_date',
        'supplier',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'expiration_date' => 'date',
        ];
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date?->isBefore(today()) ?? false;
    }
}
