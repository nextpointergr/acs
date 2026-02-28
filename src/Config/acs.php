<?php
return [

    'base_url' => env(
        'ACS_BASE_URL',
        'https://webservices.acscourier.net/ACSRestServices/api/ACSAutoRest'
    ),
    'api_key' => env('ACS_API_KEY'),
    'company_id'       => env('ACS_COMPANY_ID'),
    'company_password' => env('ACS_COMPANY_PASSWORD'),
    'user_id'          => env('ACS_USER_ID'),
    'user_password'    => env('ACS_USER_PASSWORD'),
    'language' => env('ACS_LANGUAGE', 'GR'),
    'timeout' => 30,
    'retry_attempts' => 3,
];