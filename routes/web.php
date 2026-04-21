<?php

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\DtrController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dtr', [DtrController::class, 'index'])->name('dtr.index');
    Route::get('dtr/export', [DtrController::class, 'export'])->name('dtr.export');
    Route::get('dtr/bulk-export', [DtrController::class, 'bulkExport'])->name('dtr.bulk-export');

    Route::resource('employees', EmployeeController::class);
});

require __DIR__ . '/settings.php';
