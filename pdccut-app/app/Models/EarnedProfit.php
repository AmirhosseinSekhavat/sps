<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarnedProfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'profit_type',
        'amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active profits.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include profits for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}
