<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('boards.index')
        : redirect()->route('login');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('boards.index');
    })->name('dashboard');

    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/trash', [BoardController::class, 'trash'])->name('boards.trash');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::post('/boards/{board}/restore', [BoardController::class, 'restore'])->name('boards.restore');
    Route::delete('/boards/{board}/permanently', [BoardController::class, 'permanentlyDelete'])->name('boards.permanently-delete');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

    Route::post('/boards/{board}/lists', [CardListController::class, 'store'])->name('lists.store');
    Route::put('/lists/{list}', [CardListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [CardListController::class, 'destroy'])->name('lists.destroy');
    Route::post('/lists/{list}/restore', [CardListController::class, 'restore'])->name('lists.restore');
    Route::delete('/lists/{list}/permanently', [CardListController::class, 'permanentlyDelete'])->name('lists.permanently-delete');

    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::patch('/cards/{card}/completion', [CardController::class, 'toggleComplete'])->name('cards.completion');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
});
