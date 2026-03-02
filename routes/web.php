<?php

use App\Http\Controllers\DrugController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrugController::class , 'home'])->name('home');
Route::get('/search', [DrugController::class , 'searchPage'])->name('drugs.search');
Route::get('/drugs/{id}', [DrugController::class , 'show'])->name('drugs.show');
Route::get('/drugs/fda/{slug}', [DrugController::class , 'showFda'])->name('drugs.show_fda');
Route::get('/interactions', [DrugController::class , 'interactionPage'])->name('interactions.index');