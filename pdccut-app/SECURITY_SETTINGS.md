# 🔒 تنظیمات امنیتی PDCCUT.IR

## 📋 فهرست مطالب
- [معرفی](#معرفی)
- [لایه‌های امنیتی](#لایه‌های-امنیتی)
- [Middleware های امنیتی](#middleware-های-امنیتی)
- [تنظیمات Session](#تنظیمات-session)
- [نحوه کارکرد](#نحوه-کارکرد)
- [آمار امنیتی](#آمار-امنیتی)
- [فایل‌های تغییر یافته](#فایل‌های-تغییر-یافته)
- [تست امنیت](#تست-امنیت)

## 🎯 معرفی

این مستندات شامل تمام تنظیمات امنیتی پیاده‌سازی شده در سیستم PDCCUT.IR است. سیستم از چندین لایه امنیتی برای محافظت در برابر حملات مختلف استفاده می‌کند.

## 🛡️ لایه‌های امنیتی

### ✅ لایه 1: IP Blacklist
- **هدف:** مسدودیت IP های مشکوک
- **فعال‌سازی:** پس از 20 تلاش ناموفق در 1 ساعت
- **مدت مسدودیت:** 24 ساعت
- **فایل:** `app/Http/Middleware/IpBlacklistMiddleware.php`

### ✅ لایه 2: Account Lockout
- **هدف:** قفل حساب کاربری
- **فعال‌سازی:** پس از 5 تلاش ناموفق
- **مدت قفل:** 30 دقیقه
- **فایل:** `app/Http/Middleware/AccountLockoutMiddleware.php`

### ✅ لایه 3: Rate Limiting
- **هدف:** محدودیت تعداد درخواست‌ها
- **هر کد ملی:** 3 درخواست در 15 دقیقه
- **هر IP:** 10 درخواست در 15 دقیقه
- **فایل:** `app/Http/Middleware/RateLimitOtp.php`

### ✅ لایه 4: CAPTCHA
- **هدف:** جلوگیری از حملات Bot
- **فعال‌سازی:** پس از 2 تلاش ناموفق
- **نوع:** ریاضی ساده (مثل: 5 + 3 = ?)
- **فایل:** `app/Http/Middleware/CaptchaMiddleware.php`

### ✅ لایه 5: User Enumeration Prevention
- **هدف:** جلوگیری از پیدا کردن کدهای ملی موجود
- **روش:** همیشه پیام یکسان نمایش داده می‌شود
- **فایل:** `app/Http/Controllers/AuthController.php`

## 🔧 Middleware های امنیتی

### 1. IpBlacklistMiddleware
```php
<?php
namespace App\Http\Middleware;

class IpBlacklistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        
        // Check if IP is blacklisted
        if (Cache::has('ip_blacklist_' . $ip)) {
            return response()->json([
                'error' => 'دسترسی شما مسدود شده است.',
                'message' => 'IP شما در لیست سیاه قرار دارد.'
            ], 403);
        }
        
        // Check for suspicious activity patterns
        $suspiciousKey = 'suspicious_activity_' . $ip;
        $suspiciousCount = Cache::get($suspiciousKey, 0);
        
        // If more than 20 failed attempts in 1 hour, blacklist IP
        if ($suspiciousCount >= 20) {
            Cache::put('ip_blacklist_' . $ip, true, now()->addHours(24));
            return response()->json([
                'error' => 'دسترسی شما مسدود شده است.',
                'message' => 'IP شما به دلیل فعالیت مشکوک مسدود شده است.'
            ], 403);
        }
        
        return $next($request);
    }
}
```

### 2. AccountLockoutMiddleware
```php
<?php
namespace App\Http\Middleware;

class AccountLockoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $nationalCode = $request->input('national_code');
        $ip = $request->ip();
        
        // Check if account is locked (5 failed attempts = 30 minutes lockout)
        $lockoutKey = 'account_lockout_' . $nationalCode;
        if (Cache::has($lockoutKey)) {
            $lockoutTime = Cache::get($lockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                $minutes = ceil($remainingTime / 60);
                return back()->withErrors([
                    'national_code' => "حساب کاربری شما به دلیل تلاش‌های ناموفق قفل شده است. لطفاً {$minutes} دقیقه دیگر تلاش کنید."
                ]);
            }
        }
        
        // Check if IP should be locked (10 failed attempts = 60 minutes lockout)
        $ipLockoutKey = 'ip_lockout_' . $ip;
        if (Cache::has($ipLockoutKey)) {
            $lockoutTime = Cache::get($ipLockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                $minutes = ceil($remainingTime / 60);
                return back()->withErrors([
                    'national_code' => "دسترسی شما به دلیل تلاش‌های ناموفق قفل شده است. لطفاً {$minutes} دقیقه دیگر تلاش کنید."
                ]);
            }
        }
        
        return $next($request);
    }
}
```

### 3. RateLimitOtp
```php
<?php
namespace App\Http\Middleware;

class RateLimitOtp
{
    public function handle(Request $request, Closure $next): Response
    {
        $nationalCode = $request->input('national_code');
        $ip = $request->ip();
        
        // Rate limit per national code (max 3 attempts per 15 minutes)
        $nationalCodeKey = 'otp_national_code_' . $nationalCode;
        if (RateLimiter::tooManyAttempts($nationalCodeKey, 3)) {
            $seconds = RateLimiter::availableIn($nationalCodeKey);
            return back()->withErrors([
                'national_code' => "تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً {$seconds} ثانیه دیگر تلاش کنید."
            ]);
        }
        
        // Rate limit per IP (max 10 attempts per 15 minutes)
        $ipKey = 'otp_ip_' . $ip;
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return back()->withErrors([
                'national_code' => "تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً {$seconds} ثانیه دیگر تلاش کنید."
            ]);
        }
        
        // Increment counters
        RateLimiter::hit($nationalCodeKey, 900); // 15 minutes
        RateLimiter::hit($ipKey, 900); // 15 minutes
        
        return $next($request);
    }
}
```

### 4. CaptchaMiddleware
```php
<?php
namespace App\Http\Middleware;

class CaptchaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if CAPTCHA is required (after 2 failed attempts)
        $failedAttempts = Session::get('failed_attempts', 0);
        
        if ($failedAttempts >= 2) {
            // Verify CAPTCHA
            $captchaAnswer = $request->input('captcha_answer');
            $sessionCaptcha = Session::get('captcha_question');
            
            if (!$captchaAnswer || !$sessionCaptcha || $captchaAnswer != $sessionCaptcha['answer']) {
                return back()->withErrors([
                    'captcha_answer' => 'کد امنیتی وارد شده صحیح نمی‌باشد.'
                ])->withInput();
            }
        }
        
        return $next($request);
    }
}
```

## ⚙️ تنظیمات Session

### config/session.php
```php
'lifetime' => (int) env('SESSION_LIFETIME', 30), // Reduced from 120 to 30 minutes
'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', true), // Changed to true for security
```

### مزایای امنیتی:
- **مدت کوتاه‌تر:** 30 دقیقه به جای 120 دقیقه
- **Auto-expire:** session با بستن مرورگر منقضی می‌شود
- **امنیت بیشتر:** کاهش زمان دسترسی غیرمجاز

## 🔄 نحوه کارکرد

### 1. فرآیند ورود:
```
کاربر کد ملی وارد می‌کند
    ↓
IP Blacklist بررسی می‌شود
    ↓
Account Lockout بررسی می‌شود
    ↓
Rate Limiting بررسی می‌شود
    ↓
CAPTCHA بررسی می‌شود (اگر نیاز باشد)
    ↓
OTP ارسال می‌شود
    ↓
همیشه پیام موفقیت نمایش داده می‌شود
```

### 2. بررسی امنیتی:
```php
// در AuthController@sendOtp
if ($user && $user->is_active) {
    // OTP ارسال می‌شود
    \Log::info("OTP sent successfully for national_code: {$nationalCode} from IP: {$ip}");
} else {
    // لاگ امنیتی
    \Log::warning("Failed login attempt for non-existent/inactive national_code: {$nationalCode} from IP: {$ip}");
    
    // افزایش تلاش‌های ناموفق
    $failedAttempts = session('failed_attempts', 0) + 1;
    session(['failed_attempts' => $failedAttempts]);
    
    // افزایش فعالیت مشکوک IP
    $suspiciousKey = 'suspicious_activity_' . $ip;
    $suspiciousCount = \Cache::get($suspiciousKey, 0) + 1;
    \Cache::put($suspiciousKey, $suspiciousCount, now()->addHour());
}
```

### 3. مدیریت تلاش‌های ناموفق:
```php
private function handleFailedAttempt(string $nationalCode, string $ip): void
{
    // افزایش تلاش‌های ناموفق
    $failedAttempts = session('failed_attempts', 0) + 1;
    session(['failed_attempts' => $failedAttempts]);
    
    // افزایش فعالیت مشکوک IP
    $suspiciousKey = 'suspicious_activity_' . $ip;
    $suspiciousCount = \Cache::get($suspiciousKey, 0) + 1;
    \Cache::put($suspiciousKey, $suspiciousCount, now()->addHour());
    
    // قفل حساب (5 تلاش ناموفق)
    if ($failedAttempts >= 5) {
        $lockoutKey = 'account_lockout_' . $nationalCode;
        $lockoutTime = time() + (30 * 60); // 30 دقیقه
        \Cache::put($lockoutKey, $lockoutTime, now()->addMinutes(30));
    }
    
    // قفل IP (10 تلاش ناموفق)
    if ($suspiciousCount >= 10) {
        $ipLockoutKey = 'ip_lockout_' . $ip;
        $lockoutTime = time() + (60 * 60); // 60 دقیقه
        \Cache::put($ipLockoutKey, $lockoutTime, now()->addMinutes(60));
    }
}
```

## 📊 آمار امنیتی

### ✅ محدودیت‌ها:
| نوع | تعداد | مدت | نتیجه |
|-----|--------|------|--------|
| **IP Blacklist** | 20 تلاش ناموفق | 1 ساعت | 24 ساعت مسدودیت |
| **Account Lockout** | 5 تلاش ناموفق | - | 30 دقیقه قفل |
| **IP Lockout** | 10 تلاش ناموفق | - | 60 دقیقه قفل |
| **Rate Limit** | 3 درخواست | 15 دقیقه | مسدودیت موقت |
| **CAPTCHA** | 2 تلاش ناموفق | - | فعال‌سازی |

### ✅ Session Security:
- **مدت:** 30 دقیقه
- **Auto-expire:** بله
- **Encryption:** فعال
- **Database Storage:** بله

### ✅ Logging:
- **موفق:** ورود موفق
- **ناموفق:** تلاش‌های ناموفق
- **امنیتی:** فعالیت‌های مشکوک
- **قفل:** قفل حساب/IP

## 📁 فایل‌های تغییر یافته

### 1. Middleware ها:
- `app/Http/Middleware/IpBlacklistMiddleware.php` - جدید
- `app/Http/Middleware/AccountLockoutMiddleware.php` - جدید
- `app/Http/Middleware/CaptchaMiddleware.php` - جدید
- `app/Http/Middleware/RateLimitOtp.php` - بهبود یافته

### 2. Controller:
- `app/Http/Controllers/AuthController.php` - بهبود یافته

### 3. Routes:
- `routes/web.php` - middleware های جدید اضافه شد

### 4. Config:
- `bootstrap/app.php` - alias های جدید
- `config/session.php` - تنظیمات امنیتی

### 5. Views:
- `resources/views/auth/login.blade.php` - CAPTCHA اضافه شد

## 🧪 تست امنیت

### 1. تست User Enumeration:
```bash
# باید همیشه پیام یکسان دریافت شود
curl -X POST http://127.0.0.1:8000/auth/send-otp \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "national_code=1234567890"
```

### 2. تست Rate Limiting:
```bash
# 4 بار درخواست ارسال کنید
# بار 4 باید خطای rate limit دریافت کنید
```

### 3. تست Account Lockout:
```bash
# 6 بار درخواست با کد ملی ناموجود ارسال کنید
# بار 6 باید خطای قفل حساب دریافت کنید
```

### 4. تست CAPTCHA:
```bash
# پس از 2 تلاش ناموفق، CAPTCHA باید نمایش داده شود
```

## 🚀 راه‌اندازی

### 1. نصب Dependencies:
```bash
composer install
```

### 2. Clear Cache:
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### 3. تست:
```bash
php artisan serve
# سپس به http://127.0.0.1:8000/auth/login بروید
```

## 🔍 نظارت امنیتی

### 1. لاگ‌ها:
```bash
tail -f storage/logs/laravel.log
```

### 2. Cache Status:
```bash
php artisan tinker
>>> Cache::get('ip_blacklist_127.0.0.1')
>>> Cache::get('account_lockout_1234567890')
```

### 3. Session Status:
```bash
php artisan session:table
php artisan migrate
```

## 📝 نکات مهم

### ✅ امنیت:
- **همیشه پیام یکسان** برای جلوگیری از User Enumeration
- **تاخیر تصادفی** برای جلوگیری از Timing Attacks
- **لاگ کامل** از تمام فعالیت‌ها
- **قفل خودکار** پس از تلاش‌های ناموفق

### ⚠️ هشدارها:
- **IP Blacklist** 24 ساعت فعال است
- **Account Lockout** 30 دقیقه فعال است
- **Session** 30 دقیقه منقضی می‌شود
- **CAPTCHA** پس از 2 تلاش ناموفق فعال می‌شود

### 🔧 تنظیمات قابل تغییر:
- مدت قفل‌ها در middleware ها
- تعداد تلاش‌های مجاز
- مدت session
- نوع CAPTCHA

---

**تاریخ ایجاد:** {{ date('Y-m-d H:i:s') }}  
**نسخه:** 1.0  
**وضعیت:** فعال و تست شده ✅ 