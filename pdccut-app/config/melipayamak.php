<?php 
return [
    'username' => env('MELIPAYAMAK_USERNAME', ''),
    'password' => env('MELIPAYAMAK_PASSWORD', ''),
	// OTP pattern BodyId (must be set in .env)
    'bodyid' => env('MELIPAYAMAK_BODYID'),
	// Notification pattern BodyId (must be set in .env). If not set, service will fallback to 'bodyid' when available.
    'notification_bodyid' => env('MELIPAYAMAK_NOTIFICATION_BODYID'),
    'api_url' => env('MELIPAYAMAK_API_URL', 'https://console.melipayamak.com/api/receive/balance/'),
];