<?php

use Illuminate\Support\Facades\Route;
use Modules\Movie\Http\Controllers\V1\MovieController;

Route::prefix('v1')->group(function () {

    Route::prefix('api')->group(function () {

        Route::prefix('movies')->group(function () {

            Route::controller(MovieController::class)->group(function () {
    
                Route::get('', 'index')->name('v1.api.movie.get');

                Route::get('images', [MovieController::class, 'images'])->name('v1.api.movie.images');

                Route::get('{id}', 'find')->name('v1.api.movie.find');

                Route::get('{id}/{relation}', 'findWithRelation')->name('v1.api.movie.relation.find');

            });

        });
    
    });

});