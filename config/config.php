<?php

return [
    'scopes' => [
        'ZohoBooks.fullaccess.all',
        'ZohoCRM.modules.ALL',
        'ZohoCRM.settings.ALL',
    ],

    'client_id'     => env('ZOHO_CLIENT_ID'),
    'client_secret' => env('ZOHO_CLIENT_SECRET'),

    'user_class' => '\\App\\User',

    'cache_timeout' => 3000,

    'middleware' => ['web', 'auth'],
];
