<?php

use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\AiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Drug search
    Route::get('/search', [SearchController::class , 'search']);
    Route::get('/search/instant', [SearchController::class , 'instant']);

    // AI simplifier
    Route::post('/ai/simplify', [AiController::class , 'simplify']);

    // Interaction checker
    Route::post('/interactions/check', [\App\Http\Controllers\Api\InteractionController::class , 'check']);
});