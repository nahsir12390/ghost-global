<?php

return [
    'vapid' => [
        'subject' => env('WEB_PUSH_VAPID_SUBJECT') ?: 'mailto:' . env('MAIL_FROM_ADDRESS', 'support@example.com'),
        'public_key' => env('WEB_PUSH_VAPID_PUBLIC_KEY'),
        'private_key' => env('WEB_PUSH_VAPID_PRIVATE_KEY'),
    ],
];
