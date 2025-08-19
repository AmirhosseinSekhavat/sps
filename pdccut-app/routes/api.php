<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User API routes
Route::prefix('v1/users')->name('api.users.')->group(function () {
    Route::get('/profile', [UserApiController::class, 'getProfile'])->name('profile');
    Route::get('/certificates', [UserApiController::class, 'getCertificates'])->name('certificates');
    Route::get('/notifications', [UserApiController::class, 'getNotifications'])->name('notifications');
    Route::post('/notifications/read', [UserApiController::class, 'markNotificationAsRead'])->name('notifications.read');
}); 