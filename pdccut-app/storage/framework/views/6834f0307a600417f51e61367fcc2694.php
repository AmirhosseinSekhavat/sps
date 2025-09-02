<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گواهی‌های سهام - <?php echo e($user->full_name); ?> - PDCCUT.IR</title>
    
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
                    <h1 class="mr-3 text-xl font-semibold text-gray-900">گواهی‌های سهام: <?php echo e($user->full_name); ?></h1>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    <a href="<?php echo e(route('admin.user.notifications', $user->national_code)); ?>" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-bell ml-2"></i>
                        اعلان‌ها
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

        <!-- Certificates List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-certificate text-blue-600 ml-2"></i>
                    لیست گواهی‌های سهام
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    تمام گواهی‌های سهام کاربر در سال‌های مختلف
                </p>
            </div>

            <?php if($certificates->count() > 0): ?>
            <ul class="divide-y divide-gray-200">
                <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-certificate text-blue-600 text-xl"></i>
                            </div>
                            <div class="mr-4">
                                <h4 class="text-lg font-medium text-gray-900">
                                    گواهی سهام سال <?php echo e($certificate->year); ?>

                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">مبلغ سهام:</span>
                                        <span class="font-medium text-gray-900"><?php echo e(number_format($certificate->share_amount)); ?> ریال</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">تعداد سهام:</span>
                                        <span class="font-medium text-gray-900"><?php echo e(number_format($certificate->share_count)); ?> عدد</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">سود سالانه:</span>
                                        <span class="font-medium text-gray-900"><?php echo e(number_format($certificate->annual_profit_amount)); ?> ریال</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">سود سهام پرداختی سال:</span>
                                        <span class="font-medium text-gray-900"><?php echo e(number_format($certificate->annual_payment)); ?> ریال</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <?php
                                $hasFile = $certificate->pdf_path && Storage::disk('local')->exists($certificate->pdf_path);
                            ?>

                            <?php if($hasFile): ?>
                                <a href="<?php echo e(route('admin.user.certificates.view', [$user->national_code, $certificate->year])); ?>" target="_blank" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                    <i class="fas fa-eye ml-2"></i> مشاهده PDF
                                </a>
                                <a href="<?php echo e(route('admin.user.certificates.download', [$user->national_code, $certificate->year])); ?>" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-gray-600 text-white hover:bg-gray-700">
                                    <i class="fas fa-download ml-2"></i> دانلود
                                </a>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('admin.user.generate-certificate', [$user->national_code, $certificate->year])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-yellow-600 text-white hover:bg-yellow-700">
                                        <i class="fas fa-magic ml-2"></i> تولید PDF
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <div class="px-6 py-8 text-center">
                <i class="fas fa-certificate text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">هیچ گواهی سهامی یافت نشد.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Year Selection -->
        <?php if($years->count() > 0): ?>
        <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-calendar-alt text-blue-600 ml-2"></i>
                    سال‌های موجود
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-50">
                        <?php echo e($year); ?>

                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
<?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/admin/user/certificates.blade.php ENDPATH**/ ?>