<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-user text-blue-600 ml-2"></i>
            داشبورد کاربری
        </h1>
        <p class="mt-2 text-gray-600">
            اطلاعات شخصی و مالی شما
        </p>
    </div>

    <!-- User Info -->
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center mb-6">
                <div class="h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold"><?php echo e(mb_substr($user->first_name ?? $user->name, 0, 1, 'UTF-8')); ?></span>
                </div>
                <div class="mr-4">
                    <h3 class="text-2xl leading-6 font-medium text-gray-900">
                        <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>

                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        کد ملی: <?php echo e($user->national_code); ?>

                    </p>
                    <p class="text-sm text-gray-500">
                        شماره عضویت: <?php echo e($user->membership_number); ?>

                    </p>
                </div>
            </div>

            <!-- User Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">اطلاعات شخصی</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">نام:</span>
                            <span class="font-medium"><?php echo e($user->first_name); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">نام خانوادگی:</span>
                            <span class="font-medium"><?php echo e($user->last_name); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">نام پدر:</span>
                            <span class="font-medium"><?php echo e($user->father_name ?? '—'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">شماره موبایل:</span>
                            <span class="font-medium"><?php echo e($user->mobile_number); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">وضعیت:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo e($user->is_active ? 'فعال' : 'غیرفعال'); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">اطلاعات مالی</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">مبلغ کل سهام:</span>
                            <span class="font-medium">
                                <?php if($latestCertificate): ?>
                                    <?php echo e(number_format($latestCertificate->share_amount)); ?> ریال
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">تعداد سهام:</span>
                            <span class="font-medium">
                                <?php if($latestCertificate): ?>
                                    <?php echo e(number_format($latestCertificate->share_count)); ?> عدد
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">سود سالانه:</span>
                            <span class="font-medium">
                                <?php if($latestCertificate): ?>
                                    <?php echo e(number_format($latestCertificate->annual_profit_amount)); ?> ریال
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">سود سهام پرداختی سال:</span>
                            <span class="font-medium">
                                <?php if($latestCertificate): ?>
                                    <?php echo e(number_format($latestCertificate->annual_payment)); ?> ریال
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-certificate text-2xl text-blue-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">کل گواهی‌ها</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo e($totalCertificates); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-bell text-2xl text-green-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">کل اعلان‌ها</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo e($totalNotifications); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope-open text-2xl text-yellow-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">اعلان‌های خوانده شده</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo e($totalNotifications - $unreadNotifications); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-2xl text-red-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">آخرین ورود</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo e($user->last_login_at ? \Morilog\Jalali\Jalalian::fromDateTime($user->last_login_at)->format('Y/m/d') : 'نامشخص'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earned Profits Chart (Single Bar Per Year) -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <h4 class="text-sm font-medium text-gray-800 mb-4 text-center">نمودار سودهای اکتسابی (سال‌های دارای داده)</h4>
        <?php
            $years = $earnedProfits->keys();
            $maxAmount = 0;
            foreach ($years as $y) {
                $amt = isset($earnedProfits[$y]) ? (float) $earnedProfits[$y]->amount : 0;
                if ($amt > $maxAmount) { $maxAmount = $amt; }
            }
            $maxAmount = max(10000, $maxAmount);
            $top = ceil($maxAmount / 5000) * 5000; // round to 5k
            $steps = 6; // number of horizontal grid lines
            $chartHeight = 220; // px
            $stepValue = $top / $steps;
        ?>

        <div class="flex flex-row-reverse">
            <!-- Y Axis (left in RTL) -->
            <div class="w-16 pr-2" style="height: <?php echo e($chartHeight); ?>px;">
                <div class="flex flex-col justify-between h-full text-left">
                    <?php for($i = $steps; $i >= 0; $i--): ?>
                        <?php $label = $i * $stepValue; ?>
                        <div class="text-xs text-gray-600 leading-none"><?php echo e(number_format($label)); ?></div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Chart Area (responsive + scroll on small screens) -->
            <div class="relative flex-1 overflow-x-auto">
                <div class="relative" style="height: <?php echo e($chartHeight); ?>px; min-width: 100%;">
                    <div class="absolute inset-0 pointer-events-none" style="background-image: repeating-linear-gradient(to top, #e5e7eb 0, #e5e7eb 1px, transparent 1px, transparent calc(100% / <?php echo e($steps); ?>));"></div>

                    <div class="absolute inset-0 flex items-end justify-center gap-1 sm:gap-2 md:gap-3" style="direction: ltr;">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $profit = $earnedProfits[$year] ?? null;
                                $amount = $profit ? (float) $profit->amount : 0;
                                $height = $amount > 0 ? max(6, ($amount / $top) * $chartHeight) : 6;
                            ?>
                            <div class="flex flex-col items-center justify-end h-full">
                                <div class="bg-gradient-to-t from-red-600 to-red-500 rounded w-3 sm:w-4 md:w-5 lg:w-6 xl:w-7 shadow" style="height: <?php echo e($height); ?>px;"></div>
                                <div class="mt-2 text-[10px] sm:text-xs text-gray-700"><?php echo e($year); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Certificate -->
    <?php if($latestCertificate): ?>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-certificate text-blue-600 ml-2"></i>
                آخرین گواهی سهام
            </h3>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">سال مالی:</p>
                        <p class="font-medium text-gray-900"><?php echo e($latestCertificate->year); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">مبلغ سود سالانه:</p>
                        <p class="font-medium text-gray-900"><?php echo e(number_format($latestCertificate->annual_profit_amount)); ?> ریال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">مبلغ پرداخت سالانه:</p>
                        <p class="font-medium text-gray-900"><?php echo e(number_format($latestCertificate->annual_payment)); ?> ریال</p>
                    </div>
            </div>

                <div class="mt-4">
                    <a href="<?php echo e(route('user.certificates')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-eye ml-2"></i>
                        مشاهده همه گواهی‌ها
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/user/dashboard.blade.php ENDPATH**/ ?>