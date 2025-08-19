<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class TestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {mobile : Mobile number to test} {message : Message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS service functionality';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService)
    {
        $mobile = $this->argument('mobile');
        $message = $this->argument('message');

        $this->info("Testing SMS service...");
        $this->info("Mobile: {$mobile}");
        $this->info("Message: {$message}");

        // Validate mobile number
        if (!$smsService->validateMobileNumber($mobile)) {
            $this->error("Invalid mobile number format!");
            return 1;
        }

        // Format mobile number
        $formattedMobile = $smsService->formatMobileNumber($mobile);
        $this->info("Formatted mobile: {$formattedMobile}");

        // Send test SMS
        $success = $smsService->sendNotification($formattedMobile, $message);

        if ($success) {
            $this->info("SMS sent successfully!");
            $this->info("Check logs for details.");
        } else {
            $this->error("Failed to send SMS!");
            return 1;
        }

        return 0;
    }
}
