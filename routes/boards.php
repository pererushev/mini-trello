<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('boards', 'pages::boards.index')->name('boards.index');
    Route::livewire('boards/{board}', 'pages::boards.show')->name('boards.show');
});
