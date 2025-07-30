<?php

return [
    'app_id' => env('AGORA_APP_ID'),
    'certificate' => env('AGORA_APP_CERTIFICATE'),
    'chat' => [
        'org_name' => env('AGORA_CHAT_ORG_NAME'),
        'app_name' => env('AGORA_CHAT_APP_NAME'),
        'client_id' => env('AGORA_CHAT_CLIENT_ID'),
        'client_secret' => env('AGORA_CHAT_CLIENT_SECRET'),
        'base_url' => env('AGORA_CHAT_BASE_URL', 'https://a41.easemob.com'),
    ],
];
