<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use App\Services\SmsService;

class CreateNotification extends CreateRecord
{
	protected static string $resource = NotificationResource::class;

	protected function mutateFormDataBeforeCreate(array $data): array
	{
		// For single user, just create one record normally
		return $data;
	}

	public function create(bool $another = false): void
	{
		$sendTo = $this->data['send_to'] ?? 'single';

		if ($sendTo === 'single') {
			parent::create($another);
			return;
		}

		$title = $this->data['title'] ?? '';
		$message = $this->data['message'] ?? '';

		$usersQuery = $sendTo === 'active'
			? User::query()->where('is_active', true)
			: User::query();

		$service = app(SmsService::class);
		$usersQuery->select('id', 'mobile_number', 'national_code')
			->chunk(500, function ($users) use ($title, $message, $service) {
				foreach ($users as $user) {
					NotificationModel::create([
						'user_id' => $user->id,
						'title' => $title,
						'message' => $message,
						'is_read' => false,
					]);
					if (!empty($user->mobile_number)) {
						// Send SMS with pattern arg = title
						$service->sendNotification($user->mobile_number, $title, $user->national_code);
					}
				}
			});

		// Redirect back to index without creating a single base record
		$this->redirect(NotificationResource::getUrl('index'));
	}

	protected function getRedirectUrl(): string
	{
		$sendTo = $this->data['send_to'] ?? 'single';
		if ($sendTo !== 'single') {
			return NotificationResource::getUrl('index');
		}
		return parent::getRedirectUrl();
	}

	protected function afterCreate(): void
	{
		$sendTo = $this->data['send_to'] ?? 'single';
		if ($sendTo !== 'single') {
			return;
		}
		$notification = $this->record;
		if ($notification && $notification->user) {
			$mobile = $notification->user->mobile_number;
			$national = $notification->user->national_code;
			$title = $notification->title;
			if (!empty($mobile)) {
				app(\App\Services\SmsService::class)->sendNotification($mobile, $title, $national);
			}
		}
	}
}
