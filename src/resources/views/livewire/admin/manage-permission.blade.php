    <section>
        <form wire:submit="createUser" class=" w-full space-y-4">
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
            <div class="flex items-center justify-end gap-4">
                <flux:button variant="primary" type="submit">{{ __('Create Account') }}</flux:button>
                <x-action-message class="me-3" on="user-created">{{ __('Created.') }}</x-action-message>
            </div>
        </form>

        <div class="mt-2">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">{{ __('Manage Users') }}</flux:heading>

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
                @php $usersCollection = $users ?? collect(); @endphp
                @forelse ($usersCollection as $u)
                    <div wire:key="user-{{ $u->id }}"
                        class="flex items-center justify-between rounded border p-3">
                        <div class="flex items-center gap-3">
                            <flux:badge>{{ $u->id }}</flux:badge>
                            <div class="flex flex-col">
                                <span class="font-medium">Student code: {{ $u->student_code }}</span>
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
                            <flux:button wire:click="toggleLock({{ $u->id }})"
                                wire:confirm="{{ $u->is_locked ? __('Are you sure you want to unlock this user?') : __('Are you sure you want to lock this user?') }}">
                                {{ $u->is_locked ? __('Unlock') : __('Lock') }}
                            </flux:button>

                            <flux:button variant="danger" wire:click="deleteUser({{ $u->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}">
                                {{ __('Delete') }}
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
                @if (isset($users))
                    {{ $users->onEachSide(1)->links() }}
                @endif
            </div>
        </div>
    </section>
