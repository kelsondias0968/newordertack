<?php

use App\Http\Controllers\Admin\OrderTrackAdminController;
use App\Http\Controllers\OrderTrackIssueController;
use App\Http\Controllers\OrderTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderTrackingController::class, 'index'])->name('tracking.index');
Route::post('/track/lookup', [OrderTrackingController::class, 'lookup'])->name('tracking.lookup');
Route::get('/track/{trackingCode}', [OrderTrackingController::class, 'show'])->name('tracking.show');
Route::post('/track/{trackingCode}/issues', [OrderTrackIssueController::class, 'store'])->name('tracking.issues.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/tracks', [OrderTrackAdminController::class, 'index'])->name('tracks.index');
    Route::post('/tracks', [OrderTrackAdminController::class, 'store'])->name('tracks.store');
    Route::get('/tracks/{track}', [OrderTrackAdminController::class, 'show'])->name('tracks.show');
    Route::patch('/tracks/{track}/stage', [OrderTrackAdminController::class, 'updateStage'])->name('tracks.stage.update');
});
