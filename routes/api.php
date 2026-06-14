<?php

use App\Http\Controllers\Api\OrderTrackApiController;
use App\Http\Middleware\RequireTrackingToken;
use Illuminate\Support\Facades\Route;

Route::middleware(RequireTrackingToken::class)->group(function (): void {
    Route::post('/tracks', [OrderTrackApiController::class, 'store'])->name('api.tracks.store');
    Route::get('/tracks/{trackingCode}', [OrderTrackApiController::class, 'show'])->name('api.tracks.show');
});
