<section>
    <form wire:submit="createUser" class=" w-full space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <flux:input wire:model="new_student_code" :label="__('Mã sinh viên')" type="text" required />
            </div>
            <div>
                <flux:input wire:model="new_password" :label="__('Mật khẩu')" type="password" required />
            </div>
            <div>
                <flux:select wire:model="new_role" :label="__('Vai trò')">
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                    <option value="2">Super Admin</option>
                </flux:select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-4">
            <flux:button variant="primary" type="submit">{{ __('Tạo tài khoản') }}</flux:button>
            <x-action-message class="me-3" on="user-created">{{ __('Tạo tài khoản thành công.') }}</x-action-message>
        </div>
    </form>

    <div class="mt-2">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">{{ __('Quản lý tài khoản người dùng') }}</flux:heading>

            <div class="flex items-end gap-4">
                <div class="w-64">
                    <flux:input wire:model.live.debounce.300ms="search" label=""
                        placeholder="Tìm kiếm theo mã sinh viên..." type="text" />
                </div>

                <div class="w-40">
                    <flux:select wire:model.live="perPage" label="">
                        <option value="5">5 / trang</option>
                        <option value="10">10 / trang</option>
                        <option value="15">15 / trang</option>
                        <option value="20">20 / trang</option>
                        <option value="50">50 / trang</option>
                    </flux:select>
                </div>
            </div>

        </div>


        <div class="grid gap-2">
            @php $usersCollection = $users ?? collect(); @endphp
            @forelse ($usersCollection as $u)
                <div wire:key="user-{{ $u->id }}" class="flex items-center justify-between rounded border p-3">
                    <div class="flex items-center gap-3">
                        <flux:badge>{{ $u->id }}</flux:badge>
                        <div class="flex flex-col">
                            <span class="font-medium">Mã sinh viên: {{ $u->student_code }}</span>
                            <span class="text-sm text-zinc-500">Vai trò:
                                {{ $u->role === 0 ? 'User' : ($u->role === 1 ? 'Admin' : 'Super Admin') }} | Khóa:
                                {{ $u->is_locked ? 'Đã khóa' : 'Đã mở khóa' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:select wire:change="setRole({{ $u->id }}, $event.target.value)">
                            <option value="0" @selected($u->role === 0)>User</option>
                            <option value="1" @selected($u->role === 1)>Admin</option>
                            <option value="2" @selected($u->role === 2)>Super Admin</option>
                        </flux:select>
                        <flux:button wire:click="openToggleLockModal({{ $u->id }}, {{ $u->is_locked ? 'true' : 'false' }})">
                            {{ $u->is_locked ? __('Mở khóa') : __('Khóa') }}
                        </flux:button>

                        <flux:button variant="danger" wire:click="openDeleteModal({{ $u->id }})">
                            {{ __('Xóa') }}
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

    <!-- Toggle Lock Confirmation Modal -->
    @if ($showToggleLockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click="closeToggleLockModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 shadow-xl relative" wire:click.stop="">
                <!-- Header -->
                <div
                    class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ $selectedUserLocked ? __('Mở khóa người dùng') : __('Khóa người dùng') }}
                    </h2>
                    <button wire:click="closeToggleLockModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-2xl leading-none transition-colors">
                        ×
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                        {{ $selectedUserLocked ? __('Are you sure you want to unlock this user?') : __('Are you sure you want to lock this user?') }}
                    </p>
                </div>

                <!-- Footer -->
                <div
                    class="border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
                    <flux:button wire:click="closeToggleLockModal" variant="ghost" type="button">
                        {{ __('Hủy') }}
                    </flux:button>
                    <flux:button wire:click="confirmToggleLock" variant="primary" type="button">
                        {{ __('Xác nhận') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 shadow-xl relative" wire:click.stop="">
                <!-- Header -->
                <div
                    class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('Xác nhận xóa') }}
                    </h2>
                    <button wire:click="closeDeleteModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-2xl leading-none transition-colors">
                        ×
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                        {{ __('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác.') }}
                    </p>

                    @error('delete')
                        <p class="mb-6 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer -->
                <div
                    class="border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost" type="button">
                        {{ __('Hủy') }}
                    </flux:button>
                    <flux:button wire:click="confirmDelete" variant="danger" type="button">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</section>