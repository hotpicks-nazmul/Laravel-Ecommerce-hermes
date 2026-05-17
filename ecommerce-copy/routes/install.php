<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::name('install.')->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
    Route::get('/welcome', [InstallerController::class, 'welcome'])->name('welcome.alt');

    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');

    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database/test', [InstallerController::class, 'testDatabase'])->name('database.test');
    Route::post('/database/save', [InstallerController::class, 'saveDatabase'])->name('database.save');

    Route::get('/config', [InstallerController::class, 'config'])->name('config');
    Route::post('/config/save', [InstallerController::class, 'saveConfig'])->name('config.save');

    Route::get('/admin', [InstallerController::class, 'admin'])->name('admin');
    Route::post('/admin/save', [InstallerController::class, 'saveAdmin'])->name('admin.save');

    Route::get('/install', [InstallerController::class, 'install'])->name('install');
    Route::get('/install/process', [InstallerController::class, 'process'])->name('process');
    Route::post('/install/retry', [InstallerController::class, 'retry'])->name('retry');

    Route::get('/complete', [InstallerController::class, 'complete'])->name('complete');
});
