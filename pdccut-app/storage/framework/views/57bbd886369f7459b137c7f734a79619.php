<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCCUT.IR - داشبورد کاربری</title>
    <!-- Load Font Awesome first -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Load Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/favicon.png')); ?>">

    <!-- Preload critical Farsi fonts -->
    <link rel="preload" as="font" type="font/woff2" href="<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Regular.woff2')); ?>" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Bold.woff2')); ?>" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Medium.woff2')); ?>" crossorigin>

    <style>
        @font-face {
            font-family: 'IRANSansX';
            src: url('<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Regular.woff2')); ?>') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: block;
        }
        @font-face {
            font-family: 'IRANSansX';
            src: url('<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Medium.woff2')); ?>') format('woff2');
            font-weight: 500;
            font-style: normal;
            font-display: block;
        }
        @font-face {
            font-family: 'IRANSansX';
            src: url('<?php echo e(asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Bold.woff2')); ?>') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: block;
        }
    </style>
    
    <!-- Load our custom CSS last -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <style>
        /* Ensure Farsi font is applied to all elements */
        * {
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important;
        }
        
        body { 
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important; 
            font-weight: 400;
            line-height: 1.6;
        }
        
        .rtl { direction: rtl; }
        
        /* Ensure proper font rendering for Farsi text */
        html[lang="fa"] {
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important;
        }
        
        /* Ensure Font Awesome icons are visible */
        .fas, .far, .fab {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Pro', 'Font Awesome 6 Brands' !important;
            font-weight: 900;
        }
        
        .far {
            font-weight: 400;
        }
        
        .fab {
            font-family: 'Font Awesome 6 Brands' !important;
            font-weight: 400;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="PDCCUT.IR" class="h-16 w-auto">
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="text-gray-700 hover:text-gray-900">داشبورد</a>
                    <a href="<?php echo e(route('user.certificates')); ?>" class="text-gray-700 hover:text-gray-900">گواهی‌ها</a>
                    <a href="<?php echo e(route('user.notifications')); ?>" class="relative inline-flex items-center text-gray-700 hover:text-gray-900">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="ml-2">اعلان‌ها</span>
                        <?php if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5">
                                <?php echo e($unreadNotificationsCount); ?>

                            </span>
                        <?php endif; ?>
                    </a>
                    <form method="POST" action="<?php echo e(route('auth.logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-gray-700 hover:text-gray-900">خروج</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">© 2025 PDCCUT.IR. تمامی حقوق محفوظ است.</p>
        </div>
    </footer>
</body>
</html> <?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/layouts/app.blade.php ENDPATH**/ ?>