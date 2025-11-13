<?php

use App\Http\Controllers\Content\PortalController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('/', [PortalController::class, 'login'])->name('index');
Route::post('/login', [PortalController::class, 'logindb'])->name('logindb');
Route::get('/logout', [PortalController::class, 'logout'])->name('logout');
Route::get('log-viewer', [LogViewerController::class, 'index'])->name('log-viewer');
Route::post('log-error', [PortalController::class, 'error'])->name('log-error');