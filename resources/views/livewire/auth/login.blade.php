<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">👋 Chào mừng trở lại!</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Đăng nhập để tiếp tục</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Student Code -->
            <flux:input name="student_code" :label="__('Mã sinh viên')" type="text" required autofocus
                autocomplete="username" placeholder="VD: 2254800165" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Mật khẩu')" type="password" required
                    autocomplete="current-password" placeholder="••••••••" viewable />

                @if (Route::has('password.request'))
                    <flux:link
                        class="absolute top-0 text-sm end-0 text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                        :href="route('password.request')" wire:navigate>
                        {{ __('Quên mật khẩu?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ghi nhớ đăng nhập')" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full mt-2" data-test="login-button">
                {{ __('Đăng nhập') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth>