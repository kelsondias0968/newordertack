<?php

return [
    'api_token' => env('ORDER_TRACK_API_TOKEN'),

    'automation' => [
        'in_transit_delay_days' => 8,
        'in_transit_last_day_hours' => 24,
    ],

    'email_branding' => [
        'logo_url' => env('ORDER_TRACK_EMAIL_LOGO_URL'),
        'contact' => env('ORDER_TRACK_EMAIL_CONTACT'),
        'email' => env('ORDER_TRACK_EMAIL_CONTACT_EMAIL'),
        'address' => env('ORDER_TRACK_EMAIL_ADDRESS'),
    ],
];
