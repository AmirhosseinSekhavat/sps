<?php 
return [
    'username' => env('MELIPAYAMAK_USERNAME', ''),
    'password' => env('MELIPAYAMAK_PASSWORD', ''),
    'bodyid' => env('MELIPAYAMAK_BODYID', 241964),
    // Separate pattern for notifications; set via MELIPAYAMAK_NOTIFICATION_BODYID or defaults to 12345
    'notification_bodyid' => env('MELIPAYAMAK_NOTIFICATION_BODYID', 357755),
    'api_url' => env('MELIPAYAMAK_API_URL', 'https://console.melipayamak.com/api/receive/balance/'),
];