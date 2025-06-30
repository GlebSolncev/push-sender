<?php

return [
    'private_key' => env('VAPID_PRIVATE_KEY'),
    'public_key'  => env('VAPID_PUBLIC_KEY'),
    'subject'     => 'mailto:your-email@example.com',
];