<?php

return [

    'portal_api' => [
        'username' => env('PORTAL_API_USERNAME'),
        'password' => env('PORTAL_API_PASSWORD'),
        'token_ttl' => (int) env('PORTAL_API_TOKEN_TTL', 60),
        'skdp_url' => env('PORTAL_SKDP_URL', 'http://192.168.1.200:6000/api/HistoriSukonWeb'),
        'mcu_url' => env('PORTAL_MCU_URL', 'http://192.168.1.200:5000/his/ermrj/MCURJ'),
        'document_url' => env('PORTAL_DOCUMENT_URL', 'http://192.168.1.200:1111'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
