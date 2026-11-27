<?php

return [

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    ],

    'bakong' => [
        'token' => env('BAKONG_TOKEN'),
        'account_id' => env('BAKONG_ACCOUNT_ID', 'somnang_yorn@bkrt'),
        'merchant_name' => env('BAKONG_MERCHANT_NAME', 'SOMNANG YORN'),
        'aba_account' => env('ABA_ACCOUNT_NUMBER', '007842694'),
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'none'), // plasgate | mekongsms | twilio | none
        'api_key' => env('SMS_API_KEY'),
        'api_secret' => env('SMS_API_SECRET'),
        'sender' => env('SMS_SENDER', 'LuckShop'),
        'endpoint' => env('SMS_ENDPOINT'),
        'twilio_sid' => env('TWILIO_ACCOUNT_SID'),
        'twilio_token' => env('TWILIO_AUTH_TOKEN'),
        'twilio_from' => env('TWILIO_FROM_NUMBER'),
    ],

];
