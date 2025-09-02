# راهنمای استفاده از فونت فارسی IRANSansX

## خلاصه
این پروژه از فونت فارسی IRANSansX برای نمایش متن‌های فارسی در تمام صفحات استفاده می‌کند.

## فایل‌های فونت
فونت‌های فارسی در مسیر زیر قرار دارند:
```
public/fonts/Farsi numerals/Webfonts/fonts/
├── Woff/     # فرمت WOFF
└── Woff2/    # فرمت WOFF2 (بهینه‌تر)
```

## وزن‌های موجود
- **100** - Thin
- **200** - UltraLight  
- **300** - Light
- **400** - Regular (پیش‌فرض)
- **500** - Medium
- **600** - DemiBold
- **700** - Bold
- **800** - ExtraBold
- **900** - Heavy
- **950** - Black

## صفحات به‌روزرسانی شده

### صفحات اصلی
- ✅ `welcome.blade.php` - صفحه خوش‌آمدگویی
- ✅ `layouts/app.blade.php` - Layout اصلی کاربران

### صفحات احراز هویت
- ✅ `auth/login.blade.php` - صفحه ورود
- ✅ `auth/verify-otp.blade.php` - صفحه تایید OTP

### صفحات کاربر
- ✅ `user/dashboard.blade.php` - داشبورد کاربر
- ✅ `user/certificates.blade.php` - گواهی‌های کاربر
- ✅ `user/notifications.blade.php` - اعلان‌های کاربر

### صفحات ادمین
- ✅ `admin/user/show.blade.php` - مشاهده کاربر
- ✅ `admin/user/certificates.blade.php` - گواهی‌های کاربر (ادمین)
- ✅ `admin/user/notifications.blade.php` - اعلان‌های کاربر (ادمین)
- ✅ `admin/excel/index.blade.php` - مدیریت Excel

### صفحات تست
- ✅ `test-font.blade.php` - صفحه تست فونت فارسی

## نحوه استفاده

### 1. در CSS
```css
/* استفاده از فونت فارسی */
body {
    font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif;
    font-weight: 400; /* Regular */
}

/* استفاده از وزن‌های مختلف */
.title {
    font-weight: 700; /* Bold */
}

.subtitle {
    font-weight: 500; /* Medium */
}
```

### 2. در Tailwind CSS
```html
<!-- استفاده از کلاس‌های Tailwind -->
<h1 class="font-bold">عنوان اصلی</h1>
<p class="font-medium">متن متوسط</p>
<span class="font-light">متن نازک</span>
```

### 3. در Blade Templates
```php
@extends('layouts.app')

@section('content')
<div class="font-bold text-2xl">
    عنوان با فونت فارسی
</div>
@endsection
```

## کلاس‌های کمکی
کلاس‌های CSS زیر برای کنترل وزن فونت تعریف شده‌اند:
```css
.font-thin { font-weight: 100; }
.font-ultralight { font-weight: 200; }
.font-light { font-weight: 300; }
.font-normal { font-weight: 400; }
.font-medium { font-weight: 500; }
.font-demibold { font-weight: 600; }
.font-bold { font-weight: 700; }
.font-extrabold { font-weight: 800; }
.font-heavy { font-weight: 900; }
.font-black { font-weight: 950; }
```

## تست فونت
برای تست فونت فارسی، صفحه زیر را مشاهده کنید:
```
/test-font
```

این صفحه تمام وزن‌های فونت و نمونه متن‌های فارسی و انگلیسی را نمایش می‌دهد.

## پشتیبانی از زبان‌ها
- **فارسی/عربی**: پشتیبانی کامل با اعداد فارسی
- **انگلیسی**: پشتیبانی کامل
- **RTL**: پشتیبانی کامل از راست به چپ

## نکات مهم
1. فونت به صورت خودکار در تمام صفحات اعمال می‌شود
2. از `!important` برای اطمینان از اعمال فونت استفاده شده
3. فونت‌های پشتیبان (Tahoma, Arial) برای سازگاری تعریف شده‌اند
4. از `font-display: swap` برای بهبود عملکرد استفاده شده
5. تمام صفحات admin و user اکنون از فونت فارسی استفاده می‌کنند

## عیب‌یابی
اگر فونت فارسی نمایش داده نمی‌شود:

1. بررسی کنید که فایل‌های فونت در مسیر صحیح قرار دارند
2. مطمئن شوید که CSS فونت‌ها بارگذاری شده
3. کش مرورگر را پاک کنید
4. از DevTools مرورگر برای بررسی خطاهای CSS استفاده کنید
5. مطمئن شوید که assets با `npm run build` build شده‌اند

## بهینه‌سازی
- فونت‌های WOFF2 برای مرورگرهای مدرن
- فونت‌های WOFF برای سازگاری بیشتر
- استفاده از `font-display: swap` برای بهبود Core Web Vitals
- Build کردن assets با Vite برای عملکرد بهتر

## مسیرهای مهم
- **تست فونت**: `/test-font`
- **مشاهده کاربر**: `/admin/user/{national_code}`
- **گواهی‌های کاربر**: `/admin/user/{national_code}/certificates`
- **اعلان‌های کاربر**: `/admin/user/{national_code}/notifications`
- **مدیریت Excel**: `/admin/excel` 