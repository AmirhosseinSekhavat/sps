<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_code',
        'mobile_number',
        'type',
        'message',
        'otp_code',
        'status',
        'provider',
        'provider_response_id',
        'provider_response',
        'ip_address',
        'metadata',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'sent_at',
        'delivered_at',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'national_code', 'national_code');
    }

    // Scopes
    public function scopeOtp($query)
    {
        return $query->where('type', 'otp');
    }

    public function scopeNotification($query)
    {
        return $query->where('type', 'notification');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'sent' => 'info',
            'failed' => 'danger',
            'delivered' => 'success',
            default => 'secondary'
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'otp' => 'کد تایید',
            'notification' => 'اعلان',
            default => $this->type
        };
    }

    public function getFormattedMobileAttribute()
    {
        return $this->formatMobileNumber($this->mobile_number);
    }

    private function formatMobileNumber($number)
    {
        if (str_starts_with($number, '98')) {
            return '+98 ' . substr($number, 2, 3) . ' ' . substr($number, 5, 3) . ' ' . substr($number, 8, 4);
        }
        return $number;
    }
}
