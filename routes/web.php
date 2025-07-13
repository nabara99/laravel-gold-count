<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
