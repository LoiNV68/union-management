<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $birth_date = null;
    public ?string $gender = null; // 0: Nam, 1: Nữ
    public ?string $phone_number = null;
    public ?string $address = null;
    public ?string $join_date = null;
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->full_name ?? 'Tên đang bị null';
        $this->email = $user->email ?? 'Email đang bị null';
        $this->birth_date = $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->toDateString() : null;
        $this->join_date = $user->join_date ? \Carbon\Carbon::parse($user->join_date)->toDateString() : null;
        $this->gender = $user->gender !== null ? (string) $user->gender : null;
        $this->phone_number = $user->phone_number ?? null;
        $this->address = $user->address ?? 'Địa chỉ đang bị null';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('members', 'email')->ignore($user->member->id)],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:0,1'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'], // Thêm dòng này
        ]);

        // Cập nhật thông tin chính
        $user->member->update([
            'full_name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Cập nhật Member
        if ($user->member) {
            $user->member->update([
                'full_name' => $validated['name'],
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => isset($validated['gender']) ? (int) $validated['gender'] : $user->member->gender,
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null, // Thêm dòng này
            ]);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name, email, and member info')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Full Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <div class="grid grid-cols-1 gap-4">
                    <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                    <flux:input wire:model="address" :label="__('Address')" type="text" required
                        autocomplete="address" />
                    <flux:input wire:model="join_date" :label="__('Join Date')" type="date" disabled />

                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mt-4">
                    <flux:input wire:model="birth_date" :label="__('Birth date')" type="date" />

                    <flux:select wire:model="gender" :label="__('Gender')">
                        <option value="">-- {{ __('Select') }} --</option>
                        <option value="0">{{ __('Nam') }}</option>
                        <option value="1">{{ __('Nữ') }}</option>
                    </flux:select>

                    <flux:input wire:model="phone_number" :label="__('Phone number')" type="text"
                        autocomplete="tel" />
                </div>


                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        {{-- <livewire:settings.delete-user-form /> --}}
    </x-settings.layout>
</section>
