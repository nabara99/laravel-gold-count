<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryReportController;

Route::get('/', function () {
    if(auth()->check()) {
        return redirect('/dashboard');
    }
    return view('pages.auth.login');
})->name('/');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('dashboard');
    })->name('home');
});

Route::resource('user', UserController::class)->middleware(['auth', 'isAdmin']);
Route::post('user/update-role', [UserController::class, 'updateRole'])->name('user.update-role')->middleware(['auth', 'isAdmin']);

Route::get('/locations', function () {
    return view('pages.locations.index');
})->name('locations.index')->middleware('auth', 'isAdmin');

Route::get('/workers', function () {
    return view('pages.workers.index');
})->name('workers.index')->middleware('auth', 'isAdmin');

Route::get('/absens', function () {
    return view('pages.absens.index');
})->name('absens.index')->middleware('auth', 'isAdmin');

Route::get('/stocks', function () {
    return view('pages.stocks.index');
})->name('stocks.index')->middleware('auth', 'isAdmin');

Route::get('/invest', function () {
    return view('pages.investor.index');
})->name('invest.index')->middleware('auth', 'isAdmin');

Route::get('/periods', function () {
    return view('pages.periods.index');
})->name('periods.index')->middleware('auth', 'isAdmin');

Route::get('/transactions', function () {
    return view('pages.transactions.index');
})->name('transactions.index')->middleware('auth', 'isAdmin');

Route::get('/laporan-absensi', [ReportController::class, 'index'])->name('report.absen');
Route::get('/laporan-absensi/periods/{locationId}', [ReportController::class, 'getPeriods']);
Route::get('/laporan-absensi/data', [ReportController::class, 'getReport']);

Route::prefix('laporan-penghasilan')->group(function () {
    Route::get('/', [SalaryReportController::class, 'index'])->name('laporan.index');
    Route::get('/periods/{locationId}', [SalaryReportController::class, 'getPeriods'])->name('laporan.periods');
    Route::get('/data', [SalaryReportController::class, 'getIncomeData'])->name('laporan.data');
});



