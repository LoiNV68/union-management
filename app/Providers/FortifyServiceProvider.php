<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        Fortify::username('student_code');
        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            $credentials = $request->only('student_code', 'password');
            $user = \App\Models\User::query()
                ->where('student_code', $credentials['student_code'] ?? null)
                ->first();

            if (!$user) {
                return null; // keep generic message for unknown user
            }

            if ($user->is_locked ?? false) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'),
                ]);
            }

            // Check if user has member information (except for admins)
            if ($user->role === 0 && !$user->member) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('Bạn chưa có thông tin thành viên, chưa thể đăng nhập. Vui lòng liên hệ quản trị viên.'),
                ]);
            }

            return \Illuminate\Support\Facades\Hash::check($credentials['password'] ?? '', $user->password)
                ? $user
                : null; // wrong password -> generic message
        });
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}