<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Mật khẩu')" :subheading="__('Đảm bảo mật khẩu của bạn là mật khẩu dài, ngẫu nhiên để đảm bảo an toàn')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('Mật khẩu hiện tại')"
                type="password"
                required
                autocomplete="current-password"
            />
            <flux:input
                wire:model="password"
                :label="__('Mật khẩu mới')"
                type="password"
                required
                autocomplete="new-password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Xác nhận mật khẩu')"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-password-button">
                        {{ __('Cập nhật') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Cập nhật mật khẩu thành công.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
