<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="icon-gradient-purple">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Quyền Truy Cập') }}
                </h1>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">Tạo tài khoản và phân quyền người dùng</p>
            </div>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center  gap-2 mb-4">
            <div class="icon-gradient-green">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Tạo tài khoản mới</h2>
        </div>
        <form wire:submit="createUser" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <flux:input wire:model="new_student_code" :label="__('Mã sinh viên')" type="text" required
                    placeholder="Nhập mã sinh viên..." />
                <flux:input wire:model="new_password" :label="__('Mật khẩu')" type="password" required
                    placeholder="••••••••" />
                <flux:select searchable wire:model="new_role" :label="__('Vai trò')">
                    <option value="0">👤 User</option>
                    <option value="1">🛡️ Admin</option>
                    <option value="2">👑 Super Admin</option>
                </flux:select>
            </div>
            <div class="flex items-center justify-end gap-4 pt-2">
                <x-action-message class="text-green-600 font-medium" on="user-created">✅
                    {{ __('Tạo tài khoản thành công!') }}</x-action-message>
                <flux:button variant="primary" type="submit" class="flex items-center gap-2">
                    {{ __('Tạo tài khoản') }}
                </flux:button>
            </div>
        </form>
    </div>

    <!-- User List -->
    <div class="premium-card p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2">
                <div class="icon-gradient-blue">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Danh sách người dùng</h2>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-end gap-3 w-full md:w-auto">
                <div class="w-full sm:w-64">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm theo mã sinh viên..."
                        type="text" />
                </div>
                <div class="w-full sm:w-32">
                    <flux:select wire:model.live="perPage">
                        <option value="10">10 / trang</option>
                        <option value="20">20 / trang</option>
                        <option value="50">50 / trang</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            @php $usersCollection = $users ?? collect(); @endphp
            @forelse ($usersCollection as $u)
                <div wire:key="user-{{ $u->id }}"
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div
                            class="w-10 h-10 rounded-xl {{ $u->role === 2 ? 'gradient-primary' : ($u->role === 1 ? 'bg-blue-500' : 'bg-neutral-400') }} flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ $u->role === 2 ? '👑' : ($u->role === 1 ? '🛡️' : substr($u->student_code, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $u->student_code }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $u->role === 2 ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
                                        {{ $u->role === 1 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $u->role === 0 ? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' : '' }}">
                                    {{ $u->role === 0 ? 'User' : ($u->role === 1 ? 'Admin' : 'Super Admin') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($u->is_locked)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Đã khóa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
                                        </svg>
                                        Hoạt động
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                        <div class="w-full sm:w-36">
                            <x-searchable-select @change="$wire.setRole({{ $u->id }}, $event.detail)" :value="$u->role"
                                :items="$this->roleOptions" size="sm" />
                        </div>
                        <div class="flex items-center gap-2 justify-end">
                            <flux:button
                                wire:click="openToggleLockModal({{ $u->id }}, {{ $u->is_locked ? 'true' : 'false' }})"
                                variant="ghost" size="sm" square x-on:click.stop>
                                <flux:icon.lock-closed x-show="$u->is_locked" class="size-4" />
                                <flux:icon.lock-open x-show="!$u->is_locked" class="size-4" />
                            </flux:button>
                            <flux:button wire:click="openDeleteModal({{ $u->id }})" variant="ghost" size="sm" square
                                class="text-red-500 hover:text-red-600" x-on:click.stop>
                                <flux:icon.trash class="size-4" />
                            </flux:button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <p class="text-neutral-500 font-medium">{{ __('Không tìm thấy người dùng nào.') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            @if (isset($users))
                {{ $users->onEachSide(1)->links() }}
            @endif
        </div>
    </div>

    <!-- Toggle Lock Modal -->
    @if ($showToggleLockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeToggleLockModal">
            <div class="w-full max-w-md premium-modal p-6" wire:click.stop>
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full {{ $selectedUserLocked ? 'bg-green-100 dark:bg-green-900/30' : 'bg-orange-100 dark:bg-orange-900/30' }} flex items-center justify-center">
                        @if($selectedUserLocked)
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">
                        {{ $selectedUserLocked ? __('Mở khóa người dùng') : __('Khóa người dùng') }}
                    </h3>
                    <p class="text-neutral-600 dark:text-neutral-400">
                        {{ $selectedUserLocked ? __('Người dùng sẽ có thể đăng nhập lại sau khi mở khóa.') : __('Người dùng sẽ không thể đăng nhập khi bị khóa.') }}
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeToggleLockModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="confirmToggleLock" variant="{{ $selectedUserLocked ? 'primary' : 'danger' }}">
                        {{ $selectedUserLocked ? __('Mở khóa') : __('Khóa') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeDeleteModal">
            <div class="w-full max-w-md premium-modal p-6" wire:click.stop>
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Xác nhận xóa') }}</h3>
                    <p class="text-neutral-600 dark:text-neutral-400">
                        {{ __('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác.') }}</p>
                </div>
                @error('delete')
                    <p class="mb-4 text-center text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="confirmDelete" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif
</section>