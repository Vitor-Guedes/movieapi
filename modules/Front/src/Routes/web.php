<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('front::index'));

Route::fallback(fn () => view('front::index'));
