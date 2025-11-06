<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $new_student_code = '';
    public string $new_password = '';
    public int $new_role = 0;
    public string $search = '';
    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(Auth::user()?->role === 2, 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->resetPage();
    }

    public function createUser(): void
    {
        $this->validate([
            'new_student_code' => ['required', 'string', 'max:255', 'unique:users,student_code'],
            'new_password' => ['required', 'string', 'min:6'],
            'new_role' => ['required', 'integer', 'in:0,1,2'],
        ]);

        User::query()->create([
            'student_code' => $this->new_student_code,
            'password' => $this->new_password,
            'role' => $this->new_role,
        ]);

        $this->reset(['new_student_code', 'new_password', 'new_role']);
        $this->dispatch('user-created');
    }

    public function setRole(int $userId, $role): void
    {
        abort_unless(Auth::user()?->role === 2, 403);
        $roleValue = (int) $role;
        if (!in_array($roleValue, [0, 1, 2], true)) {
            return;
        }

        $user = User::query()->findOrFail($userId);
        $user->update(['role' => $roleValue]);
        $this->dispatch('user-updated');
    }

    public function toggleLock(int $userId): void
    {
        abort_unless(Auth::user()?->role === 2, 403);
        $user = User::query()->findOrFail($userId);
        $user->update(['is_locked' => !(bool) ($user->is_locked ?? false)]);
        $this->dispatch('user-updated');
    }

    public function with(): array
    {
        return [
            'users' => User::query()
                ->when($this->search, function ($query) {
                    $query->where('student_code', 'like', '%' . $this->search . '%');
                })
                ->orderByDesc('id')
                ->paginate($this->perPage)
                ->withQueryString(),
        ];
    }
}; ?>

<section class="w-full">
    <section class="p-6">
        <form wire:submit="createUser" class="my-6 w-full space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <flux:input wire:model="new_student_code" :label="__('Student Code')" type="text" required />
                </div>
                <div>
                    <flux:input wire:model="new_password" :label="__('Password')" type="password" required />
                </div>
                <div>
                    <flux:select wire:model="new_role" :label="__('Role')">
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                        <option value="2">Super Admin</option>
                    </flux:select>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Create Account') }}</flux:button>
                <x-action-message class="me-3" on="user-created">{{ __('Created.') }}</x-action-message>
            </div>
        </form>

        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">{{ __('Users') }}</flux:heading>

                <div class="flex items-end gap-4">
                    <div class="w-64">
                        <flux:input wire:model.live.debounce.300ms="search" label=""
                            placeholder="Search by Student Code..." type="text" />
                    </div>

                    <div class="w-40">
                        <flux:select wire:model.live="perPage" label="">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </flux:select>
                    </div>
                </div>

            </div>


            <div class="grid gap-2">
                @forelse ($users as $u)
                    <div class="flex items-center justify-between rounded border p-3">
                        <div class="flex items-center gap-3">
                            <flux:badge>{{ $u->id }}</flux:badge>
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $u->student_code }}</span>
                                <span class="text-sm text-zinc-500">Role: {{ $u->role }} | Locked:
                                    {{ $u->is_locked ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:select wire:change="setRole({{ $u->id }}, $event.target.value)">
                                <option value="0" @selected($u->role === 0)>User</option>
                                <option value="1" @selected($u->role === 1)>Admin</option>
                                <option value="2" @selected($u->role === 2)>Super Admin</option>
                            </flux:select>
                            <flux:button wire:click="toggleLock({{ $u->id }})">
                                {{ $u->is_locked ? 'Unlock' : 'Lock' }}
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-500">
                        {{ __('No users found.') }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4 space-y-2">
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
    </section>
</section>
