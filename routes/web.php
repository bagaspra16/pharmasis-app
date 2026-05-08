<?php

use App\Http\Controllers\DrugController;
use App\Http\Controllers\MediCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrugController::class , 'home'])->name('home');
Route::get('/search', [DrugController::class , 'searchPage'])->name('drugs.search');
Route::get('/drugs/{id}', [DrugController::class , 'show'])->name('drugs.show');
Route::get('/drugs/fda/{slug}', [DrugController::class , 'showFda'])->name('drugs.show_fda');
Route::get('/interactions', [DrugController::class , 'interactionPage'])->name('interactions.index');

// ── MediCheck AI ─────────────────────────────────────────────────────────────
Route::post('/medicheck/analyze', [MediCheckController::class, 'analyze'])->name('medicheck.analyze');
Route::post('/medicheck/nearby', [MediCheckController::class, 'nearby'])->name('medicheck.nearby');
Route::get('/medicheck/history', [MediCheckController::class, 'history'])->name('medicheck.history');
Route::get('/medicheck/history/{id}', [MediCheckController::class, 'historyItem'])->name('medicheck.history.item');