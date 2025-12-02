<section>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Học Kỳ') }}</flux:heading>
        <flux:button wire:click="openCreateForm" variant="primary">
            {{ __('Thêm Học Kỳ') }}
        </flux:button>
    </div>

    <!-- Per Page -->
    <div class="mb-6 flex items-end gap-4">
        <div class="w-40 ml-auto">
            <flux:select wire:model.live="perPage" label="">
                <option value="10">10 / trang</option>
                <option value="20">20 / trang</option>
                <option value="50">50 / trang</option>
            </flux:select>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateForm">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $editingId ? __('Sửa Học Kỳ') : __('Thêm Học Kỳ Mới') }}
                    </flux:heading>
                    <flux:button wire:click="closeCreateForm" variant="ghost" size="sm">×</flux:button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <flux:input wire:model="school_year" label="Năm học" type="text" required
                            placeholder="VD: 2024-2025" />

                    </div>

                    <div>
                        <flux:select wire:model="semester" label="Học kỳ" required>
                            <option value="1">Học kỳ 1</option>
                            <option value="2">Học kỳ 2</option>
                            <option value="3">Học kỳ 3 (Hè)</option>
                        </flux:select>

                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <flux:button wire:click="closeCreateForm" variant="ghost" type="button">
                            {{ __('Hủy') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ $editingId ? __('Cập nhật') : __('Thêm mới') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <flux:heading size="lg" class="mb-4">{{ __('Xác nhận xóa') }}</flux:heading>
                <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                    {{ __('Bạn có chắc chắn muốn xóa học kỳ này?') }}
                </p>
                @error('delete')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="delete" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Semesters Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        STT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Năm học
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Học kỳ
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Số điểm rèn luyện
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Ngày tạo
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
                @forelse ($semesters as $index => $sem)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $semesters->firstItem() + $index }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $sem->school_year }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            <flux:badge variant="primary">
                                Học kỳ {{ $sem->semester }}{{ $sem->semester == 3 ? ' (Hè)' : '' }}
                            </flux:badge>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                        {{ $sem->training_points_count > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                {{ $sem->training_points_count }} điểm
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $sem->created_at->format('d/m/Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEditForm({{ $sem->id }})" variant="ghost" size="sm">
                                    {{ __('Sửa') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteModal({{ $sem->id }})" variant="danger" size="sm">
                                    {{ __('Xóa') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-neutral-500">
                            {{ __('Chưa có học kỳ nào.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $semesters->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="semester-created">{{ __('Đã thêm học kỳ.') }}
    </x-action-message>
    <x-action-message class="me-3" on="semester-updated">{{ __('Đã cập nhật học kỳ.') }}
    </x-action-message>
    <x-action-message class="me-3" on="semester-deleted">{{ __('Đã xóa học kỳ.') }}
    </x-action-message>
</section>