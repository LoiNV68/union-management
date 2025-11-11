<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UnionDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Default dashboard - redirects based on role
Route::get('dashboard', function (Request $request) {
    $user = $request->user();
    if (!$user) {
        return redirect()->route('login');
    }

    $role = $user->role;

    // Role = 0: Union dashboard
    if ($role == 0) {
        return redirect()->route('union.dashboard');
    }
    // Role = 1, 2: Admin dashboard
    elseif (in_array($role, [1, 2])) {
        return redirect()->route('admin.dashboard');
    }

    // Fallback to default dashboard view
    return view('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');

// Union Dashboard - Role 0
Route::get('union/dashboard', [UnionDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role'])
    ->name('union.dashboard');

// Admin Dashboard - Role 1, 2
Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role'])
    ->name('admin.dashboard');

// Admin (role=1, 2) - Management pages (Volt SFC)
Route::middleware(['auth', 'verified', 'role'])->group(function () {
    Volt::route('admin/manage-permission', 'admin.manage-permission')
        ->name('admin.permission');

    Volt::route('admin/manage-members', 'admin.manage-members')
        ->name('admin.members');

    Volt::route('admin/manage-branches', 'admin.manage-branches')
        ->name('admin.branches');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
