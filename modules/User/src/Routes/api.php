<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {

    Route::prefix('api')->group(function () {

        Route::prefix('users')->group(function () {

            Route::controller(UserController::class)->group(function () {

                Route::post('', 'store')->name('v1.api.user.store');

                Route::post('token', 'generateToken')->name('v1.api.user.token');

                // authenticated
                Route::middleware('auth:api')->group(function () {

                    Route::get('', 'user')->name('v1.api.user');

                    Route::get('logout', 'logout')->name('v1.api.user.logout');

                });

            });


        });

    });

});