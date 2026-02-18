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

    // SMS driver: 'vonage' | 'twilio' | 'log' (default — logs OTP to laravel.log for dev)
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
    ],

    // Vonage (formerly Nexmo) — recommended, free trial at https://www.vonage.com
    // Sign up with email only (no phone verification required)
    'vonage' => [
        'key'    => env('VONAGE_KEY'),
        'secret' => env('VONAGE_SECRET'),
        'from'   => env('VONAGE_FROM', 'KiddosH'),
    ],

    // Twilio (alternative) — https://www.twilio.com
    'twilio' => [
        'sid'   => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from'  => env('TWILIO_FROM'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
