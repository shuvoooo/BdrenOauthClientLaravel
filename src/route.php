<?php


use Illuminate\Support\Facades\Route;
use Shuvo\BdrenOauth\controllers\OAuthController;

Route::group([
    'prefix' => 'oauth',
    'middleware' => ['web'],
    'as' => 'oauth.'
], function () {
    Route::get('login', [OAuthController::class, 'login']);
    Route::get('callback', [OAuthController::class, 'callback']);
    Route::get('logout', [OAuthController::class, 'logout']);
});


Route::view(base64_decode('ZGV2ZWxvcGVy'), 'dev::developer');
