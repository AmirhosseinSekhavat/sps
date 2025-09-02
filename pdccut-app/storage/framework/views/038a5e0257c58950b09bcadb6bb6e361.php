<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </a>
                    <h1 class="mr-3 text-xl font-semibold text-gray-900">گواهی سهام سال <?php echo e($year); ?></h1>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    <?php if($certificate->pdf_path): ?>
                    <a href="<?php echo e(route('user.certificate.download', $year)); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-download ml-2"></i>
                        دانلود PDF
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-6">
        <!-- Certificate Details -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-certificate text-blue-600 ml-2"></i>
                    جزئیات گواهی سهام
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-3">اطلاعات مالی</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">مبلغ کل سهام:</span>
                                <span class="font-medium"><?php echo e(number_format($certificate->share_amount)); ?> ریال</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">تعداد سهام:</span>
                                <span class="font-medium"><?php echo e(number_format($certificate->share_count)); ?> عدد</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">سود سالانه:</span>
                                <span class="font-medium"><?php echo e(number_format($certificate->annual_profit_amount)); ?> ریال</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مبلغ سود:</span>
                                <span class="font-medium"><?php echo e(number_format($certificate->profit_amount)); ?> ریال</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">پرداخت سالانه:</span>
                                <span class="font-medium"><?php echo e(number_format($certificate->annual_payment)); ?> ریال</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-3">سودهای کسب شده</h4>
                        <?php if($earnedProfits->count() > 0): ?>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $earnedProfits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700"><?php echo e($profit->profit_type); ?></span>
                                <span class="font-medium text-green-700"><?php echo e(number_format($profit->amount)); ?> ریال</span>
                            </div>
                            <?php if($profit->description): ?>
                            <div class="text-xs text-gray-500 mr-4"><?php echo e($profit->description); ?></div>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <p class="text-gray-500 text-sm">هیچ سودی برای این سال ثبت نشده است.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Preview -->
        <?php if($certificate->pdf_path): ?>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-eye text-blue-600 ml-2"></i>
                    پیش‌نمایش گواهی
                </h3>
                
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <i class="fas fa-file-pdf text-6xl text-red-500 mb-4"></i>
                    <p class="text-gray-600 mb-4">برای مشاهده کامل گواهی، فایل PDF را دانلود یا مشاهده کنید.</p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="<?php echo e(route('user.certificate.view', $year)); ?>" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700" target="_blank">
                            <i class="fas fa-eye ml-2"></i>
                            مشاهده آنلاین
                        </a>
                        <a href="<?php echo e(route('user.certificate.download', $year)); ?>" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            <i class="fas fa-download ml-2"></i>
                            دانلود PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/user/certificate.blade.php ENDPATH**/ ?>