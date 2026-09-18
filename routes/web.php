<?php

use App\Http\Controllers\DrugController;
use App\Http\Controllers\MediCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrugController::class , 'home'])->name('home');
Route::redirect('/id', '/')->name('social.home');
Route::get('/search', [DrugController::class , 'searchPage'])->name('drugs.search');
Route::get('/drugs/{id}', [DrugController::class , 'show'])->name('drugs.show');
Route::get('/drugs/fda/{slug}', [DrugController::class , 'showFda'])->name('drugs.show_fda');
Route::get('/interactions', [DrugController::class , 'interactionPage'])->name('interactions.index');

// ── MediCheck AI ─────────────────────────────────────────────────────────────
Route::post('/medicheck/route', [MediCheckController::class, 'route'])->name('medicheck.route');
Route::post('/medicheck/analyze', [MediCheckController::class, 'analyze'])->name('medicheck.analyze');
Route::post('/medicheck/transcribe', [MediCheckController::class, 'transcribe'])->name('medicheck.transcribe');
Route::post('/medicheck/screen', [MediCheckController::class, 'screen'])->name('medicheck.screen');
Route::post('/medicheck/conclude', [MediCheckController::class, 'conclude'])->name('medicheck.conclude');
Route::post('/medicheck/nearby', [MediCheckController::class, 'nearby'])->name('medicheck.nearby');
Route::get('/medicheck/history', [MediCheckController::class, 'history'])->name('medicheck.history');
Route::get('/medicheck/history/{id}', [MediCheckController::class, 'historyItem'])->name('medicheck.history.item');

// ── i18n Static Asset Fallback Route ─────────────────────────────────────────
Route::get('/js/pharmasis-i18n.js', function () {
    $paths = [
        public_path('js/pharmasis-i18n.js'),
        base_path('public/js/pharmasis-i18n.js'),
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return response(file_get_contents($path), 200, [
                'Content-Type' => 'application/javascript; charset=utf-8',
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }
    }
    abort(404);
});