<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SmsLog;

class SmsService
{
	/**
	 * Send OTP via SMS
	 */
	public function sendOtp(string $mobileNumber, string $otp, string $nationalCode = null, string $ipAddress = null): bool
	{
		try {
			$mobileNumber = $this->formatMobileNumber($mobileNumber);
			if (!$this->validateMobileNumber($mobileNumber)) {
				Log::error("Invalid mobile number: {$mobileNumber}");
				return false;
			}

			$bodyId = config('melipayamak.bodyid');
			if (empty($bodyId)) {
				Log::error('MELIPAYAMAK_BODYID is not configured.');
				return false;
			}

			// Create SMS log entry
			$smsLog = SmsLog::create([
				'national_code' => $nationalCode,
				'mobile_number' => $mobileNumber,
				'type' => 'otp',
				'otp_code' => $otp,
				'status' => 'pending',
				'provider' => 'melipayamak',
				'ip_address' => $ipAddress,
				'metadata' => [
					'user_agent' => request()->userAgent(),
					'attempt' => 'initial',
				],
			]);

			$result = Http::withHeaders([
				'Content-Type' => 'application/json',
			])->post('https://console.melipayamak.com/api/send/shared/' . config('melipayamak.username'), [
				'bodyid' => (int) $bodyId,
				'to' => $mobileNumber,
				'args' => [$otp],
			]);

			$body = $result->json() ?? null;
			$success = $body['recId'] ?? false;

			if ($success) {
				// Update SMS log with success
				$smsLog->update([
					'status' => 'sent',
					'provider_response_id' => $success,
					'provider_response' => json_encode($body),
					'sent_at' => now(),
				]);

				Log::info("OTP SMS sent successfully to {$mobileNumber}. RecId: {$success}");
				return true;
			} else {
				// Update SMS log with failure
				$smsLog->update([
					'status' => 'failed',
					'provider_response' => json_encode($body),
				]);

				Log::error("Failed to send OTP SMS to {$mobileNumber}. Response: " . json_encode($body));
				return false;
			}
		} catch (\Exception $e) {
			// Update SMS log with error
			if (isset($smsLog)) {
				$smsLog->update([
					'status' => 'failed',
					'provider_response' => json_encode(['error' => $e->getMessage()]),
				]);
			}

			Log::error("Error sending OTP SMS: " . $e->getMessage());
			return false;
		}
	}

	/**
	 * Send notification via SMS
	 */
	public function sendNotification(string $mobileNumber, string $message, string $nationalCode = null, string $ipAddress = null): bool
	{
		try {
			$mobileNumber = $this->formatMobileNumber($mobileNumber);
			if (!$this->validateMobileNumber($mobileNumber)) {
				Log::error("Invalid mobile number: {$mobileNumber}");
				return false;
			}

			$notificationBodyId = config('melipayamak.notification_bodyid');
			$bodyId = $notificationBodyId ?: config('melipayamak.bodyid');
			if (empty($bodyId)) {
				Log::error('MELIPAYAMAK_NOTIFICATION_BODYID or MELIPAYAMAK_BODYID is not configured.');
				return false;
			}

			// Create SMS log entry
			$smsLog = SmsLog::create([
				'national_code' => $nationalCode,
				'mobile_number' => $mobileNumber,
				'type' => 'notification',
				'message' => $message,
				'status' => 'pending',
				'provider' => 'melipayamak',
				'ip_address' => $ipAddress,
				'metadata' => [
					'user_agent' => request()->userAgent(),
					'attempt' => 'initial',
				],
			]);

			$result = Http::withHeaders([
				'Content-Type' => 'application/json',
			])->post('https://console.melipayamak.com/api/send/shared/' . config('melipayamak.username'), [
				'bodyid' => (int) $bodyId,
				'to' => $mobileNumber,
				'args' => [$message],
			]);

			$body = $result->json() ?? null;
			$success = $body['recId'] ?? false;

			if ($success) {
				// Update SMS log with success
				$smsLog->update([
					'status' => 'sent',
					'provider_response_id' => $success,
					'provider_response' => json_encode($body),
					'sent_at' => now(),
				]);

				Log::info("Notification SMS sent successfully to {$mobileNumber}. RecId: {$success}");
				return true;
			} else {
				// Update SMS log with failure
				$smsLog->update([
					'status' => 'failed',
					'provider_response' => json_encode($body),
				]);

				Log::error("Failed to send notification SMS to {$mobileNumber}. Response: " . json_encode($body));
				return false;
			}
		} catch (\Exception $e) {
			// Update SMS log with error
			if (isset($smsLog)) {
				$smsLog->update([
					'status' => 'failed',
					'provider_response' => json_encode(['error' => $e->getMessage()]),
				]);
			}

			Log::error("Error sending notification SMS: " . $e->getMessage());
			return false;
		}
	}

	/**
	 * Get SMS delivery status
	 */
	public function getDeliveryStatus(string $providerResponseId): ?string
	{
		try {
			$result = Http::get('https://console.melipayamak.com/api/receive/delivery/' . config('melipayamak.username') . '/' . $providerResponseId);
			$body = $result->json() ?? null;
			
			// Update SMS log if found
			$smsLog = SmsLog::where('provider_response_id', $providerResponseId)->first();
			if ($smsLog) {
				$status = $this->mapDeliveryStatus($body['status'] ?? 'unknown');
				$smsLog->update([
					'status' => $status,
					'delivered_at' => $status === 'delivered' ? now() : null,
					'provider_response' => json_encode($body),
				]);
			}
			
			return $body['status'] ?? null;
		} catch (\Exception $e) {
			Log::error("Error checking delivery status: " . $e->getMessage());
			return null;
		}
	}

	/**
	 * Map delivery status to our internal status
	 */
	private function mapDeliveryStatus(string $providerStatus): string
	{
		return match($providerStatus) {
			'delivered', 'تحویل شده' => 'delivered',
			'failed', 'ناموفق' => 'failed',
			'sent', 'ارسال شده' => 'sent',
			default => 'pending'
		};
	}

	/**
	 * Get SMS balance
	 */
	public function getBalance(): ?float
	{
		try {
			$result = Http::get('https://console.melipayamak.com/api/receive/balance/' . config('melipayamak.username'));
			$body = $result->json() ?? null;
			return $body['balance'] ?? null;
		} catch (\Exception $e) {
			Log::error("Error getting balance: " . $e->getMessage());
			return null;
		}
	}

	/**
	 * Validate mobile number format
	 */
	public function validateMobileNumber(string $mobileNumber): bool
	{
		// Iranian mobile number format: 98xxxxxxxxxx
		return preg_match('/^98[0-9]{10}$/', $mobileNumber);
	}

	/**
	 * Format mobile number
	 */
	public function formatMobileNumber(string $mobileNumber): string
	{
		// Remove any non-digit characters
		$mobileNumber = preg_replace('/[^0-9]/', '', $mobileNumber);
		
		// Add 98 prefix if not present
		if (!str_starts_with($mobileNumber, '98')) {
			if (str_starts_with($mobileNumber, '0')) {
				$mobileNumber = '98' . substr($mobileNumber, 1);
			} else {
				$mobileNumber = '98' . $mobileNumber;
			}
		}
		
		return $mobileNumber;
	}
} 