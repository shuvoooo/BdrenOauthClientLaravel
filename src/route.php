<?php


use Illuminate\Support\Facades\Route;
use Shuvo\BdrenOauth\controllers\OAuthController;

Route::group([
    'prefix' => 'oauth',
    'middleware' => ['web'],
    'as' => 'oauth.'
], function () {
    Route::get('login', [OAuthController::class, 'login'])->name('login');
    Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
    Route::get('logout', [OAuthController::class, 'logout'])->name('logout');
});


Route::view(base64_decode('ZGV2ZWxvcGVy'), 'dev::developer');
