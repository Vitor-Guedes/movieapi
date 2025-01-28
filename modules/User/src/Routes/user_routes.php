<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\V1\UserController;

Route::prefix('v1')->group(function () {

    Route::prefix('api')->group(function () {

        Route::prefix('users')->group(function () {

            Route::controller(UserController::class)->group(function () {
                Route::get('unauthorized', 'unauthorized')->name('v1.api.user.unauthorized');

                Route::post('', 'store')->name('v1.api.user.store');

                Route::post('token', 'generateToken')->name('v1.api.user.token');

                // authenticated
                Route::middleware(['auth:api', 'jwt.auth', 'jwt.refresh'])->group(function () {

                    Route::get('', 'user')->name('v1.api.user');

                    Route::get('logout', 'logout')->name('v1.api.user.logout');

                });

            });


        });

    });

});