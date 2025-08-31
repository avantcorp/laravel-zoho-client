<?php

return [
    'client_id'           => env('ZOHO_CLIENT_ID'),
    'client_secret'       => env('ZOHO_CLIENT_SECRET'),
    'scopes'              => array_map(fn ($scope) => trim($scope), array_filter(explode(',', env('ZOHO_SCOPES')))),
    'token_storage_path'  => env('ZOHO_TOKEN_STORAGE_PATH', storage_path('app/zoho-token.json')),
    'redirect_on_success' => env('ZOHO_REDIRECT_ON_SUCCESS', '/'),
    'web_authentication'  => env('ZOHO_WEB_AUTHENTICATION', false),
];
