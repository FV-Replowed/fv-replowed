<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/game', function () {
    return view('game');
})->middleware(['auth', 'verified'])->name('game');

Route::get('/neighbors/pending', function () {
    return response()->json([
        'pendingNeighbors' => [],
        'neighbors' => [],
    ]);
});

Route::post('/download-file', [AssetsController::class, 'downloadAssets'])->name('download.file');
Route::get('/download-progress', [AssetsController::class, 'getProgress'])->name('download.progress');
Route::post('/extract-file', [AssetsController::class, 'extractAssets'])->name('extract.file');
Route::get('/extract-progress', [AssetsController::class, 'extractProgress'])->name('extract.progress');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
