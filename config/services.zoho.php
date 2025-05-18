<?php

return [
    'client_id'           => env('ZOHO_CLIENT_ID'),
    'client_secret'       => env('ZOHO_CLIENT_SECRET'),
    'scopes'              => [],
    'token_storage_path'  => storage_path('app/zoho-token.json'),
    'redirect_on_success' => '/',
    'web_authentication'  => false,
];
