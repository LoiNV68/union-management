<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-purple">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Học Kỳ') }}
                    </h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">Quản lý các học kỳ và năm học</p>
                </div>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="exportExcel" variant="ghost" class="gap-2">
                    <flux:icon.arrow-down-tray class="w-4 h-4" />
                    {{ __('Xuất Excel') }}
                </flux:button>
                <flux:button wire:click="openCreateForm" variant="primary">
                    {{ __('Thêm Học Kỳ') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Per Page -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-end gap-4 justify-end">
            <div class="w-32">
                <flux:select wire:model.live="perPage">
                    <option value="10">10 / trang</option>
                    <option value="20">20 / trang</option>
                    <option value="50">50 / trang</option>
                </flux:select>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeCreateForm">
            <div class="w-full max-w-md premium-modal" wire:click.stop>
                <div
                    class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="{{ $editingId ? 'icon-gradient-orange' : 'icon-gradient-green' }}">
                            @if($editingId)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            @endif
                        </div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                            {{ $editingId ? __('Sửa Học Kỳ') : __('Thêm Học Kỳ Mới') }}
                        </h2>
                    </div>
                    <button wire:click="closeCreateForm"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-2xl leading-none">×</button>
                </div>
                <div class="p-6">
                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-searchable-select wire:model.live="start_year" :label="__('Năm bắt đầu')"
                                    :items="$this->yearOptions" required />
                                <flux:error name="start_year" />
                            </div>
                            <div>
                                <x-searchable-select wire:model="end_year" :label="__('Năm kết thúc')"
                                    :items="$this->yearOptions" disabled required />
                                <flux:error name="end_year" />
                            </div>
                        </div>
                        <flux:select wire:model="semester" label="Học kỳ" required>
                            @foreach ($this->semesterOptions as $option)
                                <option value="{{ $option['value'] }}" {{ $semester == $option['value'] ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </flux:select>
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <flux:button wire:click="closeCreateForm" variant="ghost" type="button">{{ __('Hủy') }}
                            </flux:button>
                            <flux:button variant="primary" type="submit">{{ $editingId ? __('Cập nhật') : __('Thêm mới') }}
                            </flux:button>
                        </div>
                    </form>
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
                    <p class="text-neutral-600 dark:text-neutral-400">{{ __('Bạn có chắc chắn muốn xóa học kỳ này?') }}</p>
                </div>
                @error('delete')
                    <p class="mb-4 text-center text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="delete" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Semesters Table -->
    <div
        class="relative border border-neutral-200 dark:border-neutral-700 rounded-lg bg-white dark:bg-neutral-900 overflow-hidden">
        <table class="w-full">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        STT</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Năm học</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Học kỳ</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Số điểm rèn luyện</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Ngày tạo</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($semesters as $index => $sem)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $semesters->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">📅
                            {{ $sem->school_year }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <flux:badge variant="primary">
                                {{ $sem->semester == 1 ? '📚' : ($sem->semester == 2 ? '📖' : '☀️') }} Học kỳ
                                {{ $sem->semester }}{{ $sem->semester == 3 ? ' (Hè)' : '' }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sem->training_points_count > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                📊 {{ $sem->training_points_count }} điểm
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $sem->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center justify-center gap-1">
                                <flux:button wire:click="openEditForm({{ $sem->id }})" variant="ghost" size="sm"
                                    title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </flux:button>
                                <flux:button wire:click="openDeleteModal({{ $sem->id }})" variant="danger" size="sm"
                                    title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <p class="text-neutral-500 font-medium">{{ __('Chưa có học kỳ nào.') }}</p>
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