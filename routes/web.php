<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\SettingsController;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Super admin goes to admin dashboard
            return redirect()->route('admin.dashboard');
        } else {
            // All other users (patient, professional, association, store) go to buyer dashboard
            return redirect()->route('buyer.dashboard');
        }
    }

    return redirect()->route('login');
});

// Public authentication routes (for all users: admin, seller, buyer)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::prefix('admin')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/deactivate', [\App\Http\Controllers\Admin\UsersController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::post('/users/{user}/activate', [\App\Http\Controllers\Admin\UsersController::class, 'activate'])->name('admin.users.activate');

    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('admin.products.show');
    Route::post('/products/{product}/approve', [\App\Http\Controllers\Admin\ProductController::class, 'approve'])->name('admin.products.approve');
    Route::post('/products/{product}/reject', [\App\Http\Controllers\Admin\ProductController::class, 'reject'])->name('admin.products.reject');
    Route::post('/products/{product}/recover', [\App\Http\Controllers\Admin\ProductController::class, 'recover'])->name('admin.products.recover');

    Route::get('/emails', [EmailController::class, 'index'])->name('admin.emails.index');
    Route::get('/emails/{templateId}/template', [EmailController::class, 'getTemplate'])->name('admin.emails.get-template');
    Route::post('/emails/{templateId}/template', [EmailController::class, 'updateTemplate'])->name('admin.emails.update-template');
    Route::match(['get', 'post'], '/emails/{templateId}/preview', [EmailController::class, 'preview'])->name('admin.emails.preview');
    Route::post('/emails/send', [EmailController::class, 'sendEmail'])->name('admin.emails.send');

    Route::get('/email-logs', [\App\Http\Controllers\Admin\EmailLogController::class, 'index'])->name('admin.email-logs.index');
    Route::get('/email-logs/{id}', [\App\Http\Controllers\Admin\EmailLogController::class, 'show'])->name('admin.email-logs.show');
    Route::post('/email-logs/{id}/retry', [\App\Http\Controllers\Admin\EmailLogController::class, 'retry'])->name('admin.email-logs.retry');

    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings/test-gold-api', [SettingsController::class, 'testGoldApi'])->name('admin.settings.test-gold-api');
    Route::post('/settings/test-conversion-api', [SettingsController::class, 'testConversionApi'])->name('admin.settings.test-conversion-api');
});

Route::prefix('seller')->middleware(['auth', 'seller'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('seller.dashboard');
});

Route::prefix('buyer')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Buyer\DashboardController::class, 'index'])->name('buyer.dashboard');
});
