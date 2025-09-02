# PDCCUT.IR - سیستم مدیریت سهام و کاربران

## معرفی
PDCCUT.IR یک سیستم مدیریت سهام و کاربران است که با Laravel 12 و Filament Admin Panel ساخته شده است. این سیستم امکان مدیریت کاربران، گواهی‌های سهام، اعلان‌ها و تولید PDF را فراهم می‌کند.

## ویژگی‌های اصلی

### 👥 مدیریت کاربران
- ثبت و مدیریت اطلاعات کاربران (حدود 700 کاربر)
- اطلاعات شخصی: نام، نام خانوادگی، نام پدر، شماره موبایل، کد ملی، شماره عضویت
- اطلاعات مالی: مبلغ سهام، تعداد سهام، سود سالانه، مبلغ سود، پرداخت سالانه
- پشتیبانی از Excel/CSV برای ورود و خروج اطلاعات
- عملیات گروهی (فعال/غیرفعال کردن، حذف)

### 🔐 احراز هویت OTP
- ورود کاربران فقط با کد ملی
- ارسال کد تایید (OTP) به شماره موبایل
- احراز هویت بدون نیاز به رمز عبور
- پشتیبانی از صف (Queue) برای ارسال SMS

### 📊 پنل کاربری
- نمایش گواهی‌های سهام
- انتخاب سال مالی برای مشاهده PDF
- مشاهده اعلان‌های ادمین
- امکان پاسخ به اعلان‌ها

### 🏢 پنل ادمین (Filament)
- مدیریت کامل کاربران
- مدیریت گواهی‌های سهام
- مدیریت اعلان‌ها
- مدیریت تنظیمات
- آمار و نمودارها
- دسترسی سریع به پروفایل کاربران (`/u/{national_code}`)

### 📄 تولید PDF
- تولید گواهی سهام با قالب سفارشی
- پشتیبانی از Laravel Snappy
- قالب‌های RTL و فارسی
- جدول سودهای کسب شده

### 📱 API برای اپلیکیشن موبایل
- دریافت پروفایل کاربر
- دریافت گواهی‌های سهام
- مدیریت اعلان‌ها
- پاسخ به اعلان‌ها

## نیازمندی‌های سیستم

- PHP 8.2+
- Laravel 12
- SQLite/MySQL
- Composer
- Node.js & NPM (برای Vite)

## نصب و راه‌اندازی

### 1. کلون کردن پروژه
```bash
git clone <repository-url>
cd pdccut-app
```

### 2. نصب وابستگی‌ها
```bash
composer install
npm install
```

### 3. تنظیم محیط
```bash
cp .env.example .env
php artisan key:generate
```

### 4. تنظیم دیتابیس
```bash
# برای SQLite
touch database/database.sqlite
echo "DB_CONNECTION=sqlite" >> .env

# یا برای MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pdccut
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. اجرای مایگریشن‌ها
```bash
php artisan migrate
```

### 6. ایجاد کاربر ادمین
```bash
php artisan make:filament-user
```

### 7. اجرای برنامه
```bash
php artisan serve
npm run dev
```

## دسترسی‌ها

### پنل ادمین
- URL: `http://localhost:8000/admin`
- ورود با نام کاربری و رمز عبور

### پنل کاربری
- URL: `http://localhost:8000/auth/login`
- ورود فقط با کد ملی

### دسترسی سریع ادمین
- URL: `http://localhost:8000/u/{national_code}`
- مشاهده پروفایل کاربر بدون نیاز به جستجو

## ساختار فایل‌ها

```
pdccut-app/
├── app/
│   ├── Filament/           # منابع Filament
│   ├── Http/Controllers/   # کنترلرها
│   ├── Jobs/              # Job های صف
│   ├── Models/            # مدل‌ها
│   └── Services/          # سرویس‌ها
├── database/
│   ├── migrations/        # مایگریشن‌ها
│   └── seeders/          # سیدرها
├── resources/
│   ├── views/            # قالب‌های Blade
│   └── views/pdfs/       # قالب‌های PDF
└── routes/
    ├── web.php           # مسیرهای وب
    └── api.php           # مسیرهای API
```

## مدل‌های اصلی

### User
- اطلاعات کاربران
- روابط با گواهی‌ها و اعلان‌ها
- پشتیبانی از Filament

### ShareCertificate
- گواهی‌های سهام
- اطلاعات مالی سالانه
- روابط با کاربران

### Notification
- اعلان‌های ادمین
- امکان پاسخ کاربران
- وضعیت خوانده شدن

### EarnedProfit
- سودهای کسب شده
- مدیریت سالانه
- استفاده در PDF

## سرویس‌ها

### SmsService
- ارسال SMS
- اعتبارسنجی شماره موبایل
- آماده برای اتصال به Melipayamak

### PdfService
- تولید PDF گواهی سهام
- تولید گزارش سودها
- مدیریت فایل‌ها

## دستورات Artisan

### مدیریت SMS
```bash
# تست سرویس SMS
php artisan sms:test 09123456789 "پیام تست"

# اجرای صف SMS
php artisan queue:work
```

### مدیریت دیتابیس
```bash
# پاک کردن کش
php artisan config:clear
php artisan route:clear
php artisan view:clear

# بازسازی دیتابیس
php artisan migrate:fresh
```

## API Endpoints

### کاربران
```
GET  /api/v1/users/profile          # دریافت پروفایل
GET  /api/v1/users/certificates     # دریافت گواهی‌ها
GET  /api/v1/users/notifications    # دریافت اعلان‌ها
POST /api/v1/users/notifications/read   # علامت‌گذاری خوانده شده
POST /api/v1/users/notifications/reply  # پاسخ به اعلان
```

## تنظیمات

### فایل .env
```env
APP_NAME="PDCCUT.IR"
APP_LOCALE=fa
APP_FALLBACK_LOCALE=fa
APP_FAKER_LOCALE=fa_IR
TIMEZONE=Asia/Tehran

DB_CONNECTION=sqlite
# یا
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pdccut
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

## امنیت

- احراز هویت OTP
- میدلور Admin برای دسترسی‌های ادمین
- اعتبارسنجی ورودی‌ها
- محافظت CSRF
- مدیریت نشست‌ها

## توسعه

### اضافه کردن فیلد جدید
1. ایجاد مایگریشن
2. به‌روزرسانی مدل
3. به‌روزرسانی Filament Resource
4. به‌روزرسانی API

### اضافه کردن سرویس جدید
1. ایجاد کلاس سرویس
2. ثبت در Service Provider
3. استفاده در کنترلرها

## پشتیبانی

برای سوالات و مشکلات:
- بررسی لاگ‌ها در `storage/logs/`
- اجرای `php artisan route:list` برای مشاهده مسیرها
- بررسی وضعیت دیتابیس با `php artisan tinker`

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.
