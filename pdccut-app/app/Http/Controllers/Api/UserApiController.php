<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    /**
     * Get user profile by national code
     */
    public function getProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_code' => 'required|string|exists:users,national_code'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'اطلاعات وارد شده صحیح نیست',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('national_code', $request->national_code)
            ->with(['shareCertificates' => function ($query) {
                $query->latest('year')->limit(5);
            }])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'کاربر یافت نشد'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'father_name' => $user->father_name,
                    'mobile_number' => $user->mobile_number,
                    'membership_number' => $user->membership_number,
                    'national_code' => $user->national_code,
                    'is_active' => $user->is_active,
                ],
                'certificates' => $user->shareCertificates->map(function ($cert) {
                    return [
                        'id' => $cert->id,
                        'year' => $cert->year,
                        'share_amount' => $cert->share_amount,
                        'share_count' => $cert->share_count,
                        'annual_profit_amount' => $cert->annual_profit_amount,
                        'profit_amount' => $cert->profit_amount,
                        'annual_payment' => $cert->annual_payment,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get user certificates by year
     */
    public function getCertificates(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_code' => 'required|string|exists:users,national_code',
            'year' => 'nullable|integer|min:1300|max:1500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'اطلاعات وارد شده صحیح نیست',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = ShareCertificate::whereHas('user', function ($q) use ($request) {
            $q->where('national_code', $request->national_code);
        });

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        $certificates = $query->with('user:id,first_name,last_name,national_code')
            ->orderBy('year', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'year' => $cert->year,
                    'share_amount' => $cert->share_amount,
                    'share_count' => $cert->share_count,
                    'annual_profit_amount' => $cert->annual_profit_amount,
                    'profit_amount' => $cert->profit_amount,
                    'annual_payment' => $cert->annual_payment,
                    'user' => [
                        'name' => $cert->user->full_name,
                        'national_code' => $cert->user->national_code,
                    ]
                ];
            })
        ]);
    }

    /**
     * Get user notifications
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_code' => 'required|string|exists:users,national_code',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'اطلاعات وارد شده صحیح نیست',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('national_code', $request->national_code)->first();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_code' => 'required|string|exists:users,national_code',
            'notification_id' => 'required|integer|exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'اطلاعات وارد شده صحیح نیست',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('national_code', $request->national_code)->first();
        $notification = $user->notifications()->find($request->notification_id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'اعلان یافت نشد'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'اعلان به عنوان خوانده شده علامت‌گذاری شد'
        ]);
    }

}
