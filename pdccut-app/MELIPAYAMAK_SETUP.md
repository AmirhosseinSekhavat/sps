# راهنمای نصب و تنظیمات Melipayamak

## ✅ نصب شده
پکیج melipayamak با موفقیت نصب شده است.

## 📁 فایل‌های اضافه شده
- `vendor/melipayamak/laravel/` - پکیج اصلی
- `config/melipayamak.php` - فایل تنظیمات پکیج
- `config/services.php` - تنظیمات در services
- `app/Services/SmsService.php` - سرویس کامل SMS

## ⚙️ تنظیمات مورد نیاز

### 1. تنظیم متغیرهای محیطی در فایل `.env`:
```env
# Melipayamak SMS Service Configuration
MELIPAYAMAK_USERNAME=98d43ca61f9a4dfbabcf34a1d0fd60db
MELIPAYAMAK_PASSWORD=your_password_here
MELIPAYAMAK_FROM=your_sender_number_here
MELIPAYAMAK_BODYID=241964
MELIPAYAMAK_API_URL=https://console.melipayamak.com/api/receive/balance/
```

### 2. اطلاعات مورد نیاز:
- **USERNAME**: کد API (98d43ca61f9a4dfbabcf34a1d0fd60db) ✅
- **PASSWORD**: رمز عبور حساب melipayamak (اختیاری)
- **FROM**: شماره فرستنده (مثل: 5000xxx)
- **BODYID**: کد پترن (241964) ✅
- **API_URL**: آدرس API ✅

### 3. وضعیت فعلی:
- ✅ پکیج نصب شده
- ✅ تنظیمات انجام شده
- ✅ SMS ارسال می‌شود
- ⚠️ نیاز به تنظیم شماره فرستنده (FROM)

## 🚀 نحوه استفاده

### ارسال OTP:
```php
use App\Services\SmsService;

$smsService = new SmsService();
$success = $smsService->sendOtp('09123456789', '123456');
```

### ارسال پیام دلخواه:
```php
$success = $smsService->sendNotification('09123456789', 'پیام شما');
```

### بررسی موجودی:
```php
$balance = $smsService->getBalance();
```

### بررسی وضعیت ارسال:
```php
$status = $smsService->getDeliveryStatus('message_id');
```

## 🧪 تست کردن
```bash
# تست سرویس SMS
php artisan sms:test 09123456789 "پیام تست"

# یا از طریق Tinker
php artisan tinker
>>> $sms = new App\Services\SmsService();
>>> $sms->sendOtp('09123456789', '123456');
```

## 📱 فرمت شماره موبایل
سرویس به طور خودکار فرمت‌های مختلف را پشتیبانی می‌کند:
- `09123456789` ✅
- `+989123456789` ✅  
- `989123456789` ✅

## 🔍 لاگ‌ها
تمام عملیات SMS در فایل‌های لاگ ثبت می‌شوند:
- `storage/logs/laravel.log`

## ⚠️ نکات مهم
1. قبل از استفاده، حتماً اطلاعات melipayamak را در `.env` وارد کنید
2. شماره فرستنده باید از melipayamak تایید شده باشد
3. موجودی کافی در حساب melipayamak داشته باشید
4. در محیط production، لاگ‌ها را بررسی کنید

## 📞 پشتیبانی
برای مشکلات مربوط به melipayamak:
- مستندات: https://melipayamak.ir
- پشتیبانی: https://melipayamak.ir/support 