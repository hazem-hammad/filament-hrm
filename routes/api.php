<?php

use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\Banner\BannerController;
use App\Http\Controllers\Api\V1\FAQ\FAQController;
use App\Http\Controllers\Api\V1\User\Auth\AuthenticationController;
use App\Http\Controllers\Api\V1\User\Auth\OtpController;
use App\Http\Controllers\Api\V1\User\Common\ConfigurationController;
use App\Http\Controllers\Api\V1\User\Common\DeviceTokenController;
use App\Http\Controllers\Api\V1\User\Common\InformationController;
use App\Http\Controllers\Api\V1\User\Common\NotificationController;
use App\Http\Controllers\Api\V1\User\Common\UploadController;
use App\Http\Controllers\Api\V1\User\ContactUs\ContactUsController;
use App\Http\Controllers\Api\V1\User\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], routes: function () {

    Route::get('/articles', [ArticleController::class, 'index']);

    Route::get('/banners', [BannerController::class, 'index']);

    Route::get('/faqs', [FAQController::class, 'index']);

    Route::post('/contact-us', [ContactUsController::class, 'store'])->middleware(['throttle:post_data']);

    Route::get('/pages/{slug}', [InformationController::class, 'getPage']);

    Route::group(['prefix' => 'configurations'], function () {
        Route::get('/', [ConfigurationController::class, 'getConfiguration']);
    });

    Route::group(['prefix' => 'device-token'], function () {
        Route::put('/', [DeviceTokenController::class, 'updateDeviceToken'])->middleware('auth:api');
    });

    Route::group(['prefix' => 'upload'], function () {
        Route::post('', UploadController::class)->middleware(['throttle:upload']);
    });

    Route::group(['prefix' => 'auth', 'middleware' => 'throttle:auth'], function () {
        Route::post('/login', [AuthenticationController::class, 'login']);
        Route::post('/reset-password', [AuthenticationController::class, 'resetPassword']);
        Route::post('/check-user', [AuthenticationController::class, 'checkUser']);
        Route::post('/send-otp', [OtpController::class, 'send'])->middleware(['throttle:otp']);
        Route::post('/verify', [OtpController::class, 'verify']);
        Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:api']);
    });

    Route::group(['middleware' => ['auth:api', 'is_user_active']], function () {
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/', [ProfileController::class, 'get']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::put('/change-password', [ProfileController::class, 'changePassword']);
        });
    });

    Route::group(['prefix' => 'notifications', 'middleware' => ['auth:api']], function () {
        Route::get('', [NotificationController::class, 'index']);
    });

    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount'])->middleware(['auth:api']);
});
