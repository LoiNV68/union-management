<section x-data
    x-effect="$wire.showBranchModal || $wire.showDeleteModal ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Chi đoàn') }}</flux:heading>
        <flux:button wire:click="openBranchModal('create')" variant="primary">
            {{ __('Thêm Chi đoàn') }}
        </flux:button>
    </div>

    <!-- Search and Filter - Ant Design Style -->
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex-1 max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" label="" placeholder="Tìm kiếm theo tên chi đoàn..."
                type="text" />
        </div>
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="perPage" label="">
                <option value="10">10 / trang</option>
                <option value="20">20 / trang</option>
                <option value="50">50 / trang</option>
                <option value="100">100 / trang</option>
            </flux:select>
        </div>
    </div>

    <!-- Branch Modal (Create/Edit/View) -->
    @if ($showBranchModal)
        <div class="fixed inset-0 z-100 flex items-center justify-center bg-black/50 p-4" wire:click="closeBranchModal">
            <div class="w-full max-w-2xl rounded-lg bg-white dark:bg-neutral-800 shadow-xl relative" wire:click.stop="">
                <!-- Header -->
                <div
                    class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        @if ($mode === 'view')
                            {{ __('Thông tin Chi đoàn') }}
                        @elseif ($mode === 'edit')
                            {{ __('Sửa thông tin Chi đoàn') }}
                        @else
                            {{ __('Thêm Chi đoàn mới') }}
                        @endif
                    </h2>
                    <button wire:click="closeBranchModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-2xl leading-none transition-colors">
                        ×
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    <form wire:submit="saveBranch" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input wire:model="branch_name" :label="__('Tên Chi đoàn')" type="text" required
                                    :disabled="$mode === 'view'" />
                            </div>
                            <div>
                                <flux:select :disabled="$mode === 'view'" wire:model="secretary" :label="__('Bí thư')">
                                    <option value="">-- Chọn bí thư --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}
                                            ({{ $user->student_code ?? $user->email }})
                                        </option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea :disabled="$mode === 'view'" wire:model="description" :label="__('Mô tả')"
                                    rows="3" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div
                    class="border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
                    @if ($mode === 'view')
                        <flux:button wire:click="closeBranchModal" variant="ghost">
                            {{ __('Đóng') }}
                        </flux:button>
                        <flux:button wire:click="switchToEdit" variant="primary">
                            {{ __('Sửa') }}
                        </flux:button>
                    @else
                        <flux:button wire:click="closeBranchModal" variant="ghost" type="button">
                            {{ __('Hủy') }}
                        </flux:button>
                        <flux:button wire:click="saveBranch" variant="primary" type="button">
                            {{ $mode === 'edit' ? __('Cập nhật') : __('Thêm mới') }}
                        </flux:button>
                    @endif
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
                        {{ __('Bạn có chắc chắn muốn xóa chi đoàn này? Hành động này không thể hoàn tác.') }}
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
                    <flux:button wire:click="deleteBranch" variant="danger" type="button">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <div
        class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-auto-hide hover:scrollbar-thin overscroll-contain">
        <table class="w-full border-separate border-spacing-0">
            <thead>
                <tr class="bg-neutral-50 dark:bg-neutral-800">
                    <th
                        class="px-4 py-3 sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        STT
                    </th>
                    <th
                        class="px-4 py-3 sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Tên Chi đoàn
                    </th>
                    <th
                        class="px-4 py-3 sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Mô tả
                    </th>
                    <th
                        class="px-4 py-3  sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Bí thư
                    </th>
                    <th
                        class="px-4 py-3  sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Số thành viên
                    </th>
                    <th
                        class="px-4 py-3  sticky top-0 z-50 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($branches as $index => $branch)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $branches->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $branch->branch_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            <div class="max-w-xs truncate" title="{{ $branch->description }}">
                                {{ $branch->description ?? '—' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $branch->secretaryMember?->full_name }}-
                            {{ $branch->secretaryMember?->student_code }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-neutral-900 dark:text-neutral-100">
                            <span
                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $branch->members_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <flux:button wire:click="openBranchModal('view', {{ $branch->id }})" variant="ghost"
                                    size="sm">
                                    {{ __('Xem') }}
                                </flux:button>
                                <flux:button wire:click="openBranchModal('edit', {{ $branch->id }})" variant="ghost"
                                    size="sm">
                                    {{ __('Sửa') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteModal({{ $branch->id }})" variant="danger" size="sm">
                                    {{ __('Xóa') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-neutral-400 dark:text-neutral-500">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium">{{ __('Không có dữ liệu') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination - Ant Design Style -->
    @if ($branches->hasPages())
        <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
            {{ $branches->onEachSide(1)->links() }}
        </div>
    @endif
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="branch-created">{{ __('Chi đoàn đã được thêm thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="branch-updated">{{ __('Chi đoàn đã được cập nhật thành công.') }}</x-action-message>
    <x-action-message class="me-3" on="branch-deleted">{{ __('Chi đoàn đã được xóa thành công.') }}</x-action-message>
</section>