<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'first_name',
        'last_name',
        'father_name',
        'mobile_number',
        'membership_number',
        'national_code',
        'share_amount',
        'share_count',
        'annual_profit_amount',
        'annual_payment',
        'password',
        'is_active',
        'last_login_at',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'share_amount' => 'decimal:2',
            'annual_profit_amount' => 'decimal:2',
            'annual_payment' => 'decimal:2',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return 'کاربر';
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function shareCertificates(): HasMany
    {
        return $this->hasMany(ShareCertificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'national_code', 'national_code');
    }

    /**
     * Get share certificate by year
     */
    public function getShareCertificateByYear($year)
    {
        return $this->shareCertificates()->where('year', $year)->first();
    }

    /**
     * Get financial data for a specific year
     */
    public function getFinancialDataByYear($year)
    {
        $certificate = $this->getShareCertificateByYear($year);
        
        if (!$certificate) {
            return null;
        }

        return [
            'share_amount' => $certificate->share_amount,
            'share_count' => $certificate->share_count,
            'annual_profit_amount' => $certificate->annual_profit_amount,
            'annual_payment' => $certificate->annual_payment,
            'year' => $certificate->year,
        ];
    }

    /**
     * Get available financial years
     */
    public function getAvailableFinancialYears()
    {
        return $this->shareCertificates()
            ->pluck('year')
            ->unique()
            ->sort()
            ->reverse()
            ->values();
    }

    /**
     * Get latest financial year
     */
    public function getLatestFinancialYear()
    {
        return $this->shareCertificates()
            ->latest('year')
            ->value('year');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }
}
