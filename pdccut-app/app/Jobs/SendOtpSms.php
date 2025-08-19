<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $mobileNumber,
        public string $otp,
        public string $userName = '',
        public ?string $nationalCode = null,
        public ?string $ipAddress = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            $message = $this->userName 
                ? "سلام {$this->userName}، کد تایید شما: {$this->otp}"
                : "کد تایید شما: {$this->otp}";

            $success = $smsService->sendOtp($this->mobileNumber, $this->otp, $this->nationalCode, $this->ipAddress);

            if ($success) {
                Log::info("OTP SMS sent successfully to {$this->mobileNumber}");
            } else {
                Log::error("Failed to send OTP SMS to {$this->mobileNumber}");
                $this->fail(new \Exception('SMS sending failed'));
            }

        } catch (\Exception $e) {
            Log::error("Error in SendOtpSms job: " . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendOtpSms job failed for {$this->mobileNumber}: " . $exception->getMessage());
    }
}
