<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\ReviewController;

Route::prefix('v1')->group(function () {

    Route::prefix('api')->group(function () {

        Route::prefix('reviews')->middleware('auth:api')->group(function () {

            Route::controller(ReviewController::class)->group(function () {

                Route::post('', 'store')->name('v1.api.review.store');

                Route::get('', 'list')->name('v1.api.review.list');

                Route::put('/{id}', 'update')->name('v1.api.review.update');

                Route::delete('/{id}', 'destroy')->name('v1.api.review.destroy');

            });

        });

    });

});