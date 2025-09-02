<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-bell text-blue-600 ml-2"></i>
            <span>اعلان‌ها</span>
            <?php
                $localUnreadCount = $notifications->getCollection()->where('is_read', false)->count();
            ?>
            <?php if($localUnreadCount > 0): ?>
                <span class="ml-3 inline-flex items-center rounded-full bg-red-600 px-2.5 py-0.5 text-xs font-medium text-white">
                    <?php echo e($localUnreadCount); ?> خوانده نشده
                </span>
            <?php endif; ?>
        </h1>
        <p class="mt-2 text-gray-600">
            تمام اعلان‌ها و پیام‌های شما
        </p>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Notifications List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-bell text-blue-600 ml-2"></i>
                لیست اعلان‌ها
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                تمام اعلان‌ها و پیام‌های ارسالی برای شما
            </p>
        </div>

        <?php if($notifications->count() > 0): ?>
            <ul class="divide-y divide-gray-200">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="px-6 py-4 <?php echo e($notification->is_read ? 'bg-gray-50' : 'bg-white'); ?> hover:bg-gray-50">
                    <div class="flex items-start gap-4">
                        <div class="flex items-start flex-1 min-w-0">
                            <div class="flex-shrink-0 mr-2">
                                <?php if($notification->is_read): ?>
                                    <i class="fas fa-envelope-open text-gray-400 text-xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="mr-4 min-w-0 w-full">
                                <h3 class="text-lg font-medium text-gray-900 break-words text-right">
                                    <?php echo e($notification->title); ?>

                                </h3>
                                <p class="text-sm text-gray-600 mt-1 break-words whitespace-pre-line break-all text-right leading-relaxed"><?php echo e(ltrim($notification->message)); ?></p>
                                <p class="text-xs text-gray-500 mt-2 text-right">
                                <?php echo e($notification->created_at ? \Morilog\Jalali\Jalalian::fromDateTime($notification->created_at)->format('Y/m/d H:i') : 'نامشخص'); ?>

                                </p>
                            </div>
                        </div>
                        
                    <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                            <?php if(!$notification->is_read): ?>
                            <form action="<?php echo e(route('user.notifications.read', $notification->id)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700 whitespace-nowrap">
                                <i class="fas fa-check ml-2"></i>
                                    خوانده شد
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
            <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
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
</div>

    <script>
        function toggleReply(notificationId) {
    const replyForm = document.getElementById('reply-form-' + notificationId);
    if (replyForm.classList.contains('hidden')) {
        replyForm.classList.remove('hidden');
    } else {
        replyForm.classList.add('hidden');
    }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/alex/project/pdccut-1/pdccut-app/resources/views/user/notifications.blade.php ENDPATH**/ ?>