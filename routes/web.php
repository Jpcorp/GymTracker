<?php

use App\Http\Controllers\ClientExportController;
use App\Http\Controllers\ClientReportController;
use App\Livewire\Client\ClientForm;
use App\Livewire\Client\ClientList;
use App\Livewire\Client\ClientShow;
use App\Livewire\Routine\RoutineForm;
use App\Livewire\Routine\RoutineList;
use App\Livewire\Routine\RoutineShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/clients', ClientList::class)->name('clients.index');
    Route::get('/clients/create', ClientForm::class)->name('clients.create');
    Route::get('/clients/{client}/edit', ClientForm::class)->name('clients.edit');
    Route::get('/clients/{client}', ClientShow::class)->name('clients.show');
    Route::get('/clients/{client}/report', ClientReportController::class)->name('clients.report');
    Route::get('/clients/{client}/export', ClientExportController::class)->name('clients.export');

    Route::get('/clients/{client}/routines', RoutineList::class)->name('clients.routines.index');
    Route::get('/clients/{client}/routines/create', RoutineForm::class)->name('clients.routines.create');
    Route::get('/clients/{client}/routines/{routine}/edit', RoutineForm::class)->name('clients.routines.edit');
    Route::get('/clients/{client}/routines/{routine}', RoutineShow::class)->name('clients.routines.show');
});

require __DIR__.'/auth.php';
