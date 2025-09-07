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
use App\Http\Controllers\Api\V1\Common\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], routes: function () {
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{departmentId}/positions', [DepartmentController::class, 'getPositions']);
});
