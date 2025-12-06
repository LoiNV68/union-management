<section x-data
    x-effect="$wire.showBranchModal || $wire.showDeleteModal ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-teal">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Chi đoàn') }}</h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">Quản lý các chi đoàn và bí thư</p>
                </div>
            </div>
            <flux:button wire:click="openBranchModal('create')" variant="primary">
                {{ __('Thêm Chi đoàn') }}
            </flux:button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1 max-w-md">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm theo tên chi đoàn..." type="text" />
            </div>
            <div class="w-32">
                <flux:select wire:model.live="perPage">
                    <option value="10">10 / trang</option>
                    <option value="20">20 / trang</option>
                    <option value="50">50 / trang</option>
                </flux:select>
            </div>
        </div>
    </div>

    <!-- Branch Modal (Create/Edit/View) -->
    @if ($showBranchModal)
        <div class="fixed inset-0 z-100 flex items-center justify-center modal-backdrop" wire:click="closeBranchModal">
            <div class="w-full max-w-2xl premium-modal" wire:click.stop>
                <!-- Header -->
                <div class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="{{ $mode === 'view' ? 'icon-gradient-blue' : ($mode === 'edit' ? 'icon-gradient-orange' : 'icon-gradient-green') }}">
                            @if ($mode === 'view')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @elseif ($mode === 'edit')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            @endif
                        </div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                            @if ($mode === 'view')
                                {{ __('Thông tin Chi đoàn') }}
                            @elseif ($mode === 'edit')
                                {{ __('Sửa thông tin Chi đoàn') }}
                            @else
                                {{ __('Thêm Chi đoàn mới') }}
                            @endif
                        </h2>
                    </div>
                    <button wire:click="closeBranchModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-2xl leading-none transition-colors">×</button>
                </div>

                <!-- Content -->
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    <form wire:submit="saveBranch" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input wire:model="branch_name" :label="__('Tên Chi đoàn')" type="text" required :disabled="$mode === 'view'" />
                            <flux:select :disabled="$mode === 'view'" wire:model="secretary" :label="__('Bí thư')">
                                <option value="">-- Chọn bí thư --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->student_code ?? $user->email }})</option>
                                @endforeach
                            </flux:select>
                            <div class="md:col-span-2">
                                <flux:textarea :disabled="$mode === 'view'" wire:model="description" :label="__('Mô tả')" rows="3" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
                    @if ($mode === 'view')
                        <flux:button wire:click="closeBranchModal" variant="ghost">{{ __('Đóng') }}</flux:button>
                        <flux:button wire:click="switchToEdit" variant="primary">{{ __('Sửa') }}</flux:button>
                    @else
                        <flux:button wire:click="closeBranchModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                        <flux:button wire:click="saveBranch" variant="primary">{{ $mode === 'edit' ? __('Cập nhật') : __('Thêm mới') }}</flux:button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeDeleteModal">
            <div class="w-full max-w-md premium-modal p-6" wire:click.stop>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Xác nhận xóa') }}</h3>
                    <p class="text-neutral-600 dark:text-neutral-400">{{ __('Bạn có chắc chắn muốn xóa chi đoàn này? Hành động này không thể hoàn tác.') }}</p>
                </div>
                @error('delete')
                    <p class="mb-4 text-center text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="deleteBranch" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Branches Table -->
    <div class="relative border border-neutral-200 dark:border-neutral-700 rounded-lg bg-white dark:bg-neutral-900">
        <div class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-auto-hide hover:scrollbar-thin overscroll-contain">
            <table class="w-full border-separate border-spacing-0">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-800">
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">STT</th>
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Tên Chi đoàn</th>
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Mô tả</th>
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Bí thư</th>
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Số thành viên</th>
                        <th class="px-4 py-3 sticky top-0 z-10 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($branches as $index => $branch)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">{{ $branches->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $branch->branch_name }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                <div class="max-w-xs truncate" title="{{ $branch->description }}">{{ $branch->description ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $branch->secretaryMember?->full_name }} - {{ $branch->secretaryMember?->student_code }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    {{ $branch->members_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center justify-center gap-1">
                                    <flux:button wire:click="openBranchModal('view', {{ $branch->id }})" variant="ghost" size="sm" title="Xem">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </flux:button>
                                    <flux:button wire:click="openBranchModal('edit', {{ $branch->id }})" variant="ghost" size="sm" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </flux:button>
                                    <flux:button wire:click="openDeleteModal({{ $branch->id }})" variant="danger" size="sm" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-neutral-400 dark:text-neutral-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-sm font-medium">{{ __('Không có dữ liệu') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($branches->hasPages())
        <div class="mt-4">
            {{ $branches->onEachSide(1)->links() }}
        </div>
    @endif

    <!-- Action Messages -->
    <x-action-message class="me-3 text-green-600" on="branch-created">✅ {{ __('Chi đoàn đã được thêm thành công.') }}</x-action-message>
    <x-action-message class="me-3 text-green-600" on="branch-updated">✅ {{ __('Chi đoàn đã được cập nhật thành công.') }}</x-action-message>
    <x-action-message class="me-3 text-green-600" on="branch-deleted">✅ {{ __('Chi đoàn đã được xóa thành công.') }}</x-action-message>
</section>