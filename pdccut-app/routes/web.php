<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\AdminUserController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('auth.login');
});

// Compatibility alias for Laravel auth middleware (expects route named 'login')
Route::get('/login', function () {
    return redirect()->route('auth.login');
})->name('login');

// Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])
        ->middleware(['ip.blacklist', 'account.lockout', 'rate.limit.otp', 'captcha'])
        ->name('send-otp');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp.show');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp.verify');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// User dashboard routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/certificates', [UserController::class, 'certificates'])->name('user.certificates');
    Route::get('/user/certificate/{year}', [UserController::class, 'showCertificate'])->name('user.certificate');
    Route::get('/user/certificate/{year}/download', [UserController::class, 'downloadCertificate'])->name('user.certificate.download');
    Route::get('/user/certificate/{year}/view', [UserController::class, 'viewCertificatePdf'])->name('user.certificate.view');
    Route::get('/user/notifications', [UserController::class, 'notifications'])->name('user.notifications');
    Route::post('/user/notifications/{id}/read', [UserController::class, 'markNotificationAsRead'])->name('user.notifications.read');
});

// Excel import/export routes (admin only)
Route::prefix('admin/excel')->name('admin.excel.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [ExcelController::class, 'index'])->name('index');
    Route::get('/export', [ExcelController::class, 'exportUsers'])->name('export');
    Route::post('/import', [ExcelController::class, 'importUsers'])->name('import');
    Route::get('/template', [ExcelController::class, 'downloadTemplate'])->name('template');
    Route::post('/bulk-update', [ExcelController::class, 'bulkUpdate'])->name('bulk-update');
    Route::get('/search', [ExcelController::class, 'searchUsers'])->name('search');
    Route::get('/stats', [ExcelController::class, 'getUserStats'])->name('stats');
    

});

// Admin user viewer routes (admin only)
Route::prefix('admin/user')->name('admin.user.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/{national_code}', [AdminUserController::class, 'show'])->name('show');
    Route::get('/{national_code}/certificates', [AdminUserController::class, 'certificates'])->name('certificates');
    Route::get('/{national_code}/certificates/{year}/view', [AdminUserController::class, 'viewCertificatePdf'])->name('certificates.view');
    Route::get('/{national_code}/certificates/{year}/download', [AdminUserController::class, 'downloadCertificatePdf'])->name('certificates.download');
    Route::get('/{national_code}/notifications', [AdminUserController::class, 'notifications'])->name('notifications');
    Route::post('/{national_code}/certificates/{year}/generate', [AdminUserController::class, 'generateCertificatePdf'])->name('generate-certificate');
});

// Quick admin user access (admin only)
Route::get('/u/{national_code}', [AdminUserController::class, 'show'])
    ->name('admin.user.quick')
    ->middleware(['auth', 'admin']);
