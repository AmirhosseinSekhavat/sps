<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اعلان‌ها - <?php echo e($user->full_name); ?> - PDCCUT.IR</title>
    
    <!-- Load Font Awesome first -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Load Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/favicon.png')); ?>">
    
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
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="<?php echo e(route('admin.user.show', $user->national_code)); ?>" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </a>
                    <h1 class="mr-3 text-xl font-semibold text-gray-900">اعلان‌ها: <?php echo e($user->full_name); ?></h1>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    <a href="<?php echo e(route('admin.user.certificates', $user->national_code)); ?>" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-certificate ml-2"></i>
                        گواهی‌ها
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- User Info Summary -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold"><?php echo e(substr($user->first_name, 0, 1)); ?></span>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            <?php echo e($user->full_name); ?>

                        </h3>
                        <p class="text-sm text-gray-500">
                            کد ملی: <?php echo e($user->national_code); ?> | شماره عضویت: <?php echo e($user->membership_number); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-bell text-blue-600 ml-2"></i>
                    لیست اعلان‌ها
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    تمام اعلان‌های ارسال شده به کاربر
                </p>
            </div>

            <?php if($notifications->count() > 0): ?>
            <ul class="divide-y divide-gray-200">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="px-6 py-4 <?php echo e($notification->is_read ? 'bg-gray-50' : 'bg-white'); ?>">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <?php if($notification->is_read): ?>
                                    <i class="fas fa-envelope-open text-gray-400 text-xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="mr-4 flex-1">
                                <h4 class="text-lg font-medium text-gray-900">
                                    <?php echo e($notification->title); ?>

                                </h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?php echo e($notification->message); ?>

                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    <?php echo e($notification->created_at->format('Y/m/d H:i')); ?>

                                    <?php if($notification->is_read): ?>
                                        | خوانده شده در: <?php echo e($notification->read_at->format('Y/m/d H:i')); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <?php if(!$notification->is_read): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-envelope ml-1"></i>
                                جدید
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check ml-1"></i>
                                خوانده شده
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>


                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <div class="px-6 py-8 text-center">
                <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">هیچ اعلانی یافت نشد.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($notifications->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($notifications->links()); ?>

        </div>
        <?php endif; ?>
    </main>
</body>
</html>
<?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/admin/user/notifications.blade.php ENDPATH**/ ?>