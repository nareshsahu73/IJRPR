<?php

use App\Http\Controllers\PaperController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If user is already logged in, redirect based on role
    if (auth()->check()) {
        if (auth()->user()->is_admin) {
            return redirect('/admin');
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
    
    Route::resource('papers', PaperController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    
    // Use PaperController download method (works with existing files)
    Route::get('/papers/{paper}/download', [PaperController::class, 'download'])->name('papers.download');
    
    // Check paper status
    Route::get('/papers/{paper}/status', [PaperController::class, 'checkStatus'])->name('papers.status');
});

// Admin panel file download route (must be authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/papers/{id}/download', [PaperController::class, 'adminDownload'])->name('paper.download');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

require __DIR__.'/auth.php';

// Admin Two-Factor Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', function () {
        return view('admin.login');
    })->name('admin.login');
    
    Route::post('/admin/2fa/send', [\App\Http\Controllers\Admin\TwoFactorController::class, 'sendCode'])->name('admin.2fa.send');
    Route::get('/admin/2fa/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'showVerifyForm'])->name('admin.2fa.verify');
    Route::post('/admin/2fa/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verifyCode'])->name('admin.2fa.verify.code');
    Route::get('/admin/2fa/verify-token/{token}', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verifyToken'])->name('admin.2fa.verify.token');
});

// Override Filament admin logout to redirect to home page
Route::post('/admin/logout', function (Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('filament.admin.auth.logout');
