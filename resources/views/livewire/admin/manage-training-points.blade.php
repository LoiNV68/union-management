<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-orange">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Điểm Rèn Luyện') }}</h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">Quản lý điểm rèn luyện của đoàn viên theo học kỳ</p>
                </div>
            </div>
            <flux:button wire:click="openCreateForm" variant="primary" class="gap-2">
                {{ __('Thêm Điểm') }}
            </flux:button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm kiếm theo tên hoặc mã SV..." type="text" />
            </div>
            <div class="w-48">
                <flux:select wire:model.live="filterSemesterId">
                    <option value="">📅 Tất cả học kỳ</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">
                            {{ $semester->school_year }} - HK{{ $semester->semester }}
                        </option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-48">
                <flux:select wire:model.live="filterBranchId">
                    <option value="">🏛️ Tất cả chi hội</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </flux:select>
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

    <!-- Create/Edit Modal -->
    @if ($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeCreateForm">
            <div class="w-full max-w-xl premium-modal p-6" wire:click.stop>
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="{{ $editingId ? 'icon-gradient-blue' : 'icon-gradient-green' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $editingId ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $editingId ? __('Sửa Điểm Rèn Luyện') : __('Thêm Điểm Rèn Luyện Mới') }}
                        </h2>
                    </div>
                    <flux:button wire:click="closeCreateForm" variant="ghost" size="sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </flux:button>
                </div>

                <form wire:submit="save" class="space-y-5">
                    <flux:select wire:model="member_id" label="Thành viên" required :disabled="$editingId ? true : false">
                        <option value="">Chọn thành viên</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">
                                {{ $member->full_name }} - {{ $member->user?->student_code }}
                            </option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="semester_id" label="Học kỳ" required :disabled="$editingId ? true : false">
                        <option value="">Chọn học kỳ</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}">
                                {{ $semester->school_year }} - Học kỳ {{ $semester->semester }}
                            </option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="point" label="Điểm" type="number" step="0.01" min="0" max="100" required placeholder="0.00" />

                    <flux:textarea wire:model="note" label="Nhận xét" placeholder="Nhập nhận xét (tùy chọn)..." />

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                        <flux:button wire:click="closeCreateForm" variant="ghost" type="button">{{ __('Hủy') }}</flux:button>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeDeleteModal">
            <div class="w-full max-w-md premium-modal p-6" wire:click.stop>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Xác nhận xóa') }}</h3>
                    <p class="text-neutral-600 dark:text-neutral-400">{{ __('Bạn có chắc chắn muốn xóa điểm rèn luyện này? Hành động này không thể hoàn tác.') }}</p>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="delete" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if ($showViewModal && $viewingTrainingPoint)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" wire:click="closeViewModal">
            <div class="w-full max-w-xl premium-modal p-6" wire:click.stop>
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="icon-gradient-purple">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Chi tiết Điểm Rèn Luyện') }}</h2>
                    </div>
                    <flux:button wire:click="closeViewModal" variant="ghost" size="sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </flux:button>
                </div>

                <div class="space-y-4">
                    <!-- Score Display -->
                    <div class="text-center p-6 rounded-xl {{ $viewingTrainingPoint->point >= 90 ? 'bg-green-50 dark:bg-green-900/20' : ($viewingTrainingPoint->point >= 80 ? 'bg-blue-50 dark:bg-blue-900/20' : ($viewingTrainingPoint->point >= 65 ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-red-50 dark:bg-red-900/20')) }}">
                        <p class="text-sm font-medium text-neutral-500 mb-2">Điểm Rèn Luyện</p>
                        <p class="text-5xl font-bold {{ $viewingTrainingPoint->point >= 90 ? 'text-green-600' : ($viewingTrainingPoint->point >= 80 ? 'text-blue-600' : ($viewingTrainingPoint->point >= 65 ? 'text-yellow-600' : 'text-red-600')) }}">
                            {{ number_format($viewingTrainingPoint->point, 1) }}
                        </p>
                        <span class="inline-flex mt-2 px-3 py-1 rounded-full text-sm font-semibold {{ $viewingTrainingPoint->point >= 90 ? 'score-excellent' : ($viewingTrainingPoint->point >= 80 ? 'score-good' : ($viewingTrainingPoint->point >= 65 ? 'score-average' : 'score-below')) }}">
                            {{ $viewingTrainingPoint->point >= 90 ? 'Xuất sắc' : ($viewingTrainingPoint->point >= 80 ? 'Tốt' : ($viewingTrainingPoint->point >= 65 ? 'Khá' : 'Trung bình')) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Mã SV') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->member->user?->student_code ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Họ và tên') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->member->full_name }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Chi hội') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->member->branch?->branch_name ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Học kỳ') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->semester->school_year }} - HK{{ $viewingTrainingPoint->semester->semester }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Người cập nhật') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->updater?->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">{{ __('Ngày cập nhật') }}</p>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $viewingTrainingPoint->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-2">{{ __('Nhận xét') }}</p>
                        <p class="text-sm text-neutral-900 dark:text-neutral-100 whitespace-pre-line">{{ $viewingTrainingPoint->note ?: __('Không có nhận xét') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button wire:click="closeViewModal" variant="primary">{{ __('Đóng') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Training Points Table -->
    <div class="premium-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Mã SV</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Họ và tên</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Chi hội</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Học kỳ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Điểm</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Cập nhật bởi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">Ngày</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-neutral-500">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                    @forelse ($trainingPoints as $index => $tp)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-500">{{ $trainingPoints->firstItem() + $index }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-mono text-neutral-900 dark:text-neutral-100">{{ $tp->member->user?->student_code ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $tp->member->full_name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $tp->member->branch?->branch_name ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $tp->semester->school_year }} - HK{{ $tp->semester->semester }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold
                                    {{ $tp->point >= 90 ? 'score-excellent' : '' }}
                                    {{ $tp->point >= 80 && $tp->point < 90 ? 'score-good' : '' }}
                                    {{ $tp->point >= 65 && $tp->point < 80 ? 'score-average' : '' }}
                                    {{ $tp->point < 65 ? 'score-below' : '' }}">
                                    {{ number_format($tp->point, 1) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $tp->updater?->full_name ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-500">{{ $tp->updated_at->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button wire:click="openViewModal({{ $tp->id }})" variant="ghost" size="sm" title="Xem chi tiết">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </flux:button>
                                    <flux:button wire:click="openEditForm({{ $tp->id }})" variant="ghost" size="sm" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </flux:button>
                                    <flux:button wire:click="openDeleteModal({{ $tp->id }})" variant="danger" size="sm" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    </div>
                                    <p class="text-neutral-500 font-medium">{{ __('Chưa có điểm rèn luyện nào.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $trainingPoints->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="training-point-created">{{ __('Đã thêm điểm rèn luyện.') }}</x-action-message>
    <x-action-message class="me-3" on="training-point-updated">{{ __('Đã cập nhật điểm rèn luyện.') }}</x-action-message>
    <x-action-message class="me-3" on="training-point-deleted">{{ __('Đã xóa điểm rèn luyện.') }}</x-action-message>
</section>