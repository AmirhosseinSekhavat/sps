<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\OtpCode;
use App\Jobs\SendOtpSms;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Generate CAPTCHA question if needed
        $this->generateCaptchaIfNeeded();
        
        return view('auth.login');
    }

    /**
     * Send OTP to user's mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'national_code' => 'required|string|size:10',
        ]);

        $nationalCode = $request->national_code;
        $ip = $request->ip();
        
        // Always show success message for security (prevents user enumeration)
        // Check if user exists and is active in the background
        $user = User::where('national_code', $nationalCode)->first();
        
        if ($user && $user->is_active) {
            // User exists and is active - generate and send OTP
            $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Save OTP to database
            OtpCode::create([
                'national_code' => $user->national_code,
                'otp_code' => $otpCode,
                'expires_at' => now()->addMinutes(5),
            ]);

            // Send OTP via SMS using queue
            SendOtpSms::dispatch(
                $user->mobile_number,
                $otpCode,
                $user->first_name,
                $user->national_code,
                $ip
            );

            // Log successful OTP generation
            \Log::info("OTP sent successfully for national_code: {$nationalCode} from IP: {$ip}");
            
            // Reset failed attempts for this national code
            session()->forget('failed_attempts');
        } else {
            // User doesn't exist or is inactive - log for security monitoring
            \Log::warning("Failed login attempt for non-existent/inactive national_code: {$nationalCode} from IP: {$ip}");
            
            // Increment failed attempts
            $failedAttempts = session('failed_attempts', 0) + 1;
            session(['failed_attempts' => $failedAttempts]);
            
            // Increment suspicious activity for IP
            $suspiciousKey = 'suspicious_activity_' . $ip;
            $suspiciousCount = \Cache::get($suspiciousKey, 0) + 1;
            \Cache::put($suspiciousKey, $suspiciousCount, now()->addHour());
            
            // Simulate OTP generation delay to prevent timing attacks
            usleep(rand(100000, 500000)); // Random delay between 100-500ms
        }

        // Always set session and redirect to prevent user enumeration
        session(['otp_sent' => true, 'national_code' => $nationalCode]);
        
        return redirect()->route('auth.verify-otp.show')->with('success', 'کد تایید ارسال شد.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp()
    {
        if (!session('otp_sent')) {
            return redirect()->route('auth.login');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $nationalCode = session('national_code');
        $otpCode = $request->otp_code;
        $ip = $request->ip();

        // First check if user exists and is active
        $user = User::where('national_code', $nationalCode)->first();
        
        if (!$user || !$user->is_active) {
            // Log security attempt
            \Log::warning("Login attempt with invalid/inactive national_code: {$nationalCode} from IP: {$ip}");
            
            // Increment failed attempts and check for lockout
            $this->handleFailedAttempt($nationalCode, $ip);
            
            // Clear session and redirect to login
            session()->forget(['otp_sent', 'national_code']);
            return redirect()->route('auth.login')->withErrors(['national_code' => 'اطلاعات وارد شده صحیح نمی‌باشد.']);
        }

        // Check OTP validity
        $otpRecord = OtpCode::where('national_code', $nationalCode)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->first();

        if (!$otpRecord) {
            // Log failed OTP attempt
            \Log::warning("Failed OTP verification for national_code: {$nationalCode} from IP: {$ip}");
            
            // Increment failed attempts and check for lockout
            $this->handleFailedAttempt($nationalCode, $ip);
            
            return back()->withErrors(['otp_code' => 'کد تایید نامعتبر یا منقضی شده است.']);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Login user
        Auth::login($user);

        // Clear session and reset failed attempts
        session()->forget(['otp_sent', 'national_code', 'failed_attempts']);

        // Log successful login
        \Log::info("Successful login for user: {$user->id} ({$nationalCode}) from IP: {$ip}");

        return redirect()->route('user.dashboard')->with('success', 'خوش آمدید!');
    }

    /**
     * Generate CAPTCHA question if needed
     */
    private function generateCaptchaIfNeeded(): void
    {
        $failedAttempts = session('failed_attempts', 0);
        
        if ($failedAttempts >= 2) {
            // Generate simple math CAPTCHA
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            $answer = $num1 + $num2;
            
            session(['captcha_question' => [
                'question' => "{$num1} + {$num2} = ?",
                'answer' => $answer
            ]]);
        } else {
            // Ensure captcha is cleared if not needed
            session()->forget('captcha_question');
        }
    }

    /**
     * Handle failed login attempts and implement lockout
     */
    private function handleFailedAttempt(string $nationalCode, string $ip): void
    {
        // Increment failed attempts for national code
        $failedAttempts = session('failed_attempts', 0) + 1;
        session(['failed_attempts' => $failedAttempts]);
        
        // Increment suspicious activity for IP
        $suspiciousKey = 'suspicious_activity_' . $ip;
        $suspiciousCount = \Cache::get($suspiciousKey, 0) + 1;
        \Cache::put($suspiciousKey, $suspiciousCount, now()->addHour());
        
        // Check if account should be locked (5 failed attempts)
        if ($failedAttempts >= 5) {
            $lockoutKey = 'account_lockout_' . $nationalCode;
            $lockoutTime = time() + (30 * 60); // 30 minutes
            \Cache::put($lockoutKey, $lockoutTime, now()->addMinutes(30));
            
            \Log::warning("Account locked due to multiple failed attempts: {$nationalCode} from IP: {$ip}");
        }
        
        // Check if IP should be locked (10 failed attempts)
        if ($suspiciousCount >= 10) {
            $ipLockoutKey = 'ip_lockout_' . $ip;
            $lockoutTime = time() + (60 * 60); // 60 minutes
            \Cache::put($ipLockoutKey, $lockoutTime, now()->addMinutes(60));
            
            \Log::warning("IP locked due to multiple failed attempts: {$ip} for national_code: {$nationalCode}");
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login')->with('success', 'خروج موفقیت‌آمیز بود.');
    }
}
