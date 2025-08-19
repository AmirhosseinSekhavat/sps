<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'share_amount',
        'share_count',
        'annual_profit_amount',
        'annual_payment',
        'pdf_path',
    ];

    protected $casts = [
        'share_amount' => 'decimal:2',
        'annual_profit_amount' => 'decimal:2',
        'annual_payment' => 'decimal:2',
        'year' => 'integer',
        'share_count' => 'integer',
    ];

    /**
     * Get the user that owns the share certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the earned profits for this year.
     */
    public function earnedProfits()
    {
        return EarnedProfit::where('year', $this->year)->get();
    }
}
