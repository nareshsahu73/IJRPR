<?php

use App\Http\Controllers\PaperController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If user is already logged in, redirect based on role
    if (auth()->check()) {
        if (auth()->user()->is_admin) {
            return redirect('/myweb');
        }
        return redirect()->route('dashboard');
    }
    
    // Show welcome page for guests
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('papers.index');
    })->name('dashboard');
    
    Route::get('/papers', [PaperController::class, 'index'])->name('papers.index');
    Route::get('/papers/create', [PaperController::class, 'create'])->name('papers.create');
    Route::post('/papers', [PaperController::class, 'store'])->name('papers.store');
    Route::delete('/papers/{paper}', [PaperController::class, 'destroy'])->name('papers.destroy');

    // Clean URLs without 'papers' folder
    Route::get('/{id}/download', [PaperController::class, 'download'])->name('papers.download')->where('id', '[0-9]+');
    Route::get('/{id}/status', [PaperController::class, 'checkStatus'])->name('papers.status')->where('id', '[0-9]+');
});

// Admin panel file download route (must be authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/papers/{id}/download', [PaperController::class, 'adminDownload'])->name('paper.download');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

require __DIR__.'/auth.php';

// Forgot Password routes
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// Admin Password Reset (hidden route - file: auth_service.php)
Route::middleware('guest')->group(function () {
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/account-recovery', [\App\Http\Controllers\Auth\auth_service::class, 'showForm'])->name('admin.password.request');
    Route::post('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/account-recovery', [\App\Http\Controllers\Auth\auth_service::class, 'sendResetLink'])->name('admin.password.email');
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/account-recovery/reset/{token}', [\App\Http\Controllers\Auth\auth_service::class, 'showResetForm'])->name('admin.password.reset.form');
    Route::post('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/account-recovery/reset', [\App\Http\Controllers\Auth\auth_service::class, 'resetPassword'])->name('admin.password.update');
});
Route::middleware('guest')->group(function () {
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/login', function () {
        return view('myweb.login');
    })->name('admin.login');
    
    Route::post('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/2fa/send', [\App\Http\Controllers\Admin\TwoFactorController::class, 'sendCode'])->name('admin.2fa.send');
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/2fa/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'showVerifyForm'])->name('admin.2fa.verify');
    Route::post('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/2fa/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verifyCode'])->name('admin.2fa.verify.code');
    Route::get('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/2fa/verify-token/{token}', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verifyToken'])->name('admin.2fa.verify.token');
});

// Override Filament admin logout to redirect to home page
Route::post('/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42') . '/logout', function (Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('filament.admin.auth.logout');
