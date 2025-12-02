<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Xác nhận mật khẩu')"
            :description="__('Đảm bảo mật khẩu của bạn là mật khẩu dài, ngẫu nhiên để đảm bảo an toàn')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('Xác nhận') }}
            </flux:button>
        </form>
    </div>
</x-layouts.auth>
