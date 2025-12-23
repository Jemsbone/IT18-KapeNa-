<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialiteController;

Route::get('/home', function () {
    return view('welcome');
});

// Google OAuth callback
Route::get('/auth/google/callback', [SocialiteController::class, 'googleCallback']);
