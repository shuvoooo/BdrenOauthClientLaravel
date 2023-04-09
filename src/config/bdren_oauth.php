<?php

return [
    'oauth_client_id' => env('OAUTH_CLIENT_ID'),
    'oauth_client_secret' => env('OAUTH_CLIENT_SECRET'),
    'oauth_base_url' => env('OAUTH_BASE_URL',"https://accounts.bdren.net.bd"),
    'oauth_user_model' => env('OAUTH_USER_MODEL', 'App\Models\User'),
    'oauth_success_url' => env('OAUTH_SUCCESS_URL', '/'),
    'oauth_error_url' => env('OAUTH_ERROR_URL', '/'),
];
