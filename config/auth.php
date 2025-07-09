<?php

use App\Models\Customer;
use App\Models\User;

return [


    'defaults' => [
        'guard' => env('AUTH_GUARD', 'user'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'customers'),
    ],


    'guards' => [
        'user' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'company' => [ // New guard for delivery companies
            'driver' => 'session',
            'provider' => 'company',
        ],

        'customer' => [
            'driver' => 'sanctum',
            'provider' => 'customers'
        ]
    ],


    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],

        'customers' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ],

        'company' => [ // New provider for delivery companies
            'driver' => 'eloquent',
            'model' => App\Models\DeliveryCompany::class,
        ],
    ],


    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'customers' => [
            'provider' => 'customers',
            'table' => 'customers',
            'expire' => 60,
            'throttle' => 60,
        ],

        'company' => [ // Separate password reset settings
            'provider' => 'company',
            'table' => 'password_reset_tokens', // Can use the same table, identified by email
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
