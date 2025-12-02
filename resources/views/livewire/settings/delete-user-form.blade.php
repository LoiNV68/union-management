<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            data-test="delete-user-button">
            {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Bạn có chắc chắn muốn xóa tài khoản của bạn?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Khi tài khoản của bạn được xóa, tất cả tài nguyên và dữ liệu của bạn sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận bạn muốn xóa tài khoản của bạn vĩnh viễn.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Mật khẩu')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Hủy') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit" data-test="confirm-delete-user-button">
                    {{ __('Xóa tài khoản') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>