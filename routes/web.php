<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
