<section x-data
    x-effect="$wire.showBranchModal || $wire.showDeleteModal ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Chi đoàn') }}</flux:heading>
        <flux:button wire:click="openBranchModal('create')" variant="primary">
            {{ __('Thêm Chi đoàn') }}
        </flux:button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label="" placeholder="Tìm kiếm theo tên chi đoàn..."
                type="text" />
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

    <!-- Branch Modal (Create/Edit/View) -->
    @if ($showBranchModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click="closeBranchModal">
            <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-neutral-800 shadow-2xl relative"
                wire:click.stop="">
                <!-- Header -->
                <div
                    class="sticky top-0 bg-white dark:bg-neutral-800 border-b rounded-t-2xl border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        @if ($mode === 'view')
                            {{ __('Thông tin Chi đoàn') }}
                        @elseif ($mode === 'edit')
                            {{ __('Sửa thông tin Chi đoàn') }}
                        @else
                            {{ __('Thêm Chi đoàn mới') }}
                        @endif
                    </h2>
                    <button wire:click="closeBranchModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-3xl font-bold leading-none transition-colors">
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
                                <flux:select :disabled="$mode === 'view'" wire:model="secretary"
                                    :label="__('Bí thư')">
                                    <option value="">-- Chọn bí thư --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}
                                            ({{ $user->student_code ?? $user->email }})
                                        </option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea :disabled="$mode === 'view'" wire:model="description"
                                    :label="__('Mô tả')" rows="3" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div
                    class="sticky bottom-0 rounded-b-2xl bg-neutral-50 dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
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
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-neutral-800 shadow-2xl relative"
                wire:click.stop="">
                <!-- Header -->
                <div
                    class="sticky top-0 bg-white dark:bg-neutral-800 border-b rounded-t-2xl border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('Xác nhận xóa') }}
                    </h2>
                    <button wire:click="closeDeleteModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-3xl font-bold leading-none transition-colors">
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
                    class="sticky bottom-0 rounded-b-2xl bg-neutral-50 dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
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

    <!-- Branches Table -->
    <div class="relative border border-neutral-200 dark:border-neutral-700 rounded-lg">
        <!-- Header cố định -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-neutral-100 w-[60px]">
                            STT
                        </th>
                        <th
                            class="px-4 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-neutral-100 bg-neutral-50 dark:bg-neutral-800 shadow-right">
                            Tên Chi đoàn
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                            Mô tả
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                            Bí thư
                        </th>

                        <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                            Số thành viên
                        </th>
                        <th
                            class="px-4 py-3 text-center text-sm font-semibold text-neutral-900 dark:text-neutral-100 lg:sticky right-0 z-30 bg-neutral-50 dark:bg-neutral-800 shadow-left">
                            Thao tác
                        </th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Nội dung cuộn -->
        <div
            class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-auto-hide hover:scrollbar-thin overscroll-contain">
            <table class="w-full border-collapse relative">
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                    @forelse ($branches as $index => $branch)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                            <td
                                class="px-4 py-3 lg:sticky left-0 bg-white dark:bg-neutral-900 z-30 text-sm text-neutral-900 dark:text-neutral-100 w-[60px] shadow-right">
                                {{ $branches->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100 shadow-right">
                                {{ $branch->branch_name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $branch->description ?? 'N/A' }}
                            </td>
                            <td class=" px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $branch->secretary?->name ?? 'Chưa có' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                {{ $branch->members_count }}
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white dark:bg-neutral-900 z-20 shadow-left">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <flux:button wire:click="openBranchModal('view', {{ $branch->id }})"
                                        variant="ghost" size="sm">
                                        {{ __('Xem') }}
                                    </flux:button>
                                    <flux:button wire:click="openBranchModal('edit', {{ $branch->id }})"
                                        variant="ghost" size="sm">
                                        {{ __('Sửa') }}
                                    </flux:button>
                                    <flux:button wire:click="openDeleteModal({{ $branch->id }})" variant="danger"
                                        size="sm">
                                        {{ __('Xóa') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-neutral-500">
                                {{ __('Không tìm thấy chi đoàn nào.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 space-y-2">
        {{ $branches->onEachSide(1)->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3"
        on="branch-created">{{ __('Chi đoàn đã được thêm thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="branch-updated">{{ __('Chi đoàn đã được cập nhật thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="branch-deleted">{{ __('Chi đoàn đã được xóa thành công.') }}</x-action-message>
</section>
