@php
    // View rendered by App\Livewire\Admin\ManageActivities
@endphp


<section>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Hoạt động') }}</flux:heading>
        <flux:button wire:click="openCreateForm" variant="primary">
            {{ __('Thêm Hoạt động') }}
        </flux:button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label=""
                placeholder="Tìm kiếm theo tên hoạt động, địa điểm..." type="text" />
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

    <!-- Create/Edit Form Modal -->
    @if ($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateForm">
            <div class="w-full max-w-3xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $editingId ? __('Sửa Hoạt động') : __('Thêm Hoạt động mới') }}
                    </flux:heading>
                    <flux:button wire:click="closeCreateForm" variant="ghost" size="sm">×</flux:button>
                </div>

                <form wire:submit="saveActivity" class="space-y-4">
                    <div>
                        <flux:input wire:model="activity_name" :label="__('Tên Hoạt động')" type="text" required />
                    </div>
                    <div>
                        <flux:textarea wire:model="description" :label="__('Mô tả')" rows="3" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="start_date" :label="__('Ngày bắt đầu')" type="date" required />
                        </div>
                        <div>
                            <flux:input wire:model="end_date" :label="__('Ngày kết thúc')" type="date" required />
                        </div>
                    </div>
                    <div>
                        <flux:input wire:model="location" :label="__('Địa điểm')" type="text" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:select wire:model="type" :label="__('Loại hoạt động')" required>
                                <option value="0">Thể dục</option>
                                <option value="1">Văn hóa</option>
                                <option value="2">Khác</option>
                            </flux:select>
                        </div>
                        <div>
                            <flux:input wire:model="max_participants" :label="__('Số lượng tối đa')" type="number"
                                min="1" />
                        </div>
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

    <!-- View Activity Modal -->
    @if ($viewingId && $viewingActivity)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeViewModal">
            <div class="w-full max-w-2xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Thông tin Hoạt động') }}</flux:heading>
                    <flux:button wire:click="closeViewModal" variant="ghost" size="sm">×</flux:button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Tên Hoạt động</p>
                            <p class="text-lg font-semibold">{{ $viewingActivity->activity_name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Mô tả</p>
                            <p class="text-lg">{{ $viewingActivity->description ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Ngày bắt đầu</p>
                            <p class="text-lg">{{ $viewingActivity->start_date?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Ngày kết thúc</p>
                            <p class="text-lg">{{ $viewingActivity->end_date?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Địa điểm</p>
                            <p class="text-lg">{{ $viewingActivity->location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Loại hoạt động</p>
                            <p class="text-lg">
                                @switch($viewingActivity->type)
                                    @case(0)
                                        Thể dục
                                    @break

                                    @case(1)
                                        Văn hóa
                                    @break

                                    @default
                                        Khác
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Người tạo</p>
                            <p class="text-lg">
                                {{ $viewingActivity->user?->full_name . ' - ' . $viewingActivity->user?->student_code ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Số lượng đăng ký</p>
                            <p class="text-lg">{{ $viewingActivity->registrations_count }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Số lượng tối đa</p>
                            <p class="text-lg">{{ $viewingActivity->max_participants ?? 'Không giới hạn' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <flux:button wire:click="closeViewModal" variant="ghost">
                            {{ __('Đóng') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif



    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <flux:heading size="lg" class="mb-4">{{ __('Xác nhận xóa') }}</flux:heading>
                <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                    {{ __('Bạn có chắc chắn muốn xóa hoạt động này? Hành động này không thể hoàn tác.') }}
                </p>
                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeDeleteModal" variant="ghost" type="button">
                        {{ __('Hủy') }}
                    </flux:button>
                    <flux:button wire:click="deleteActivity" variant="danger" type="button">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Activities List -->
    <div class="grid gap-2">
        @forelse ($activities as $activity)
            <div class="flex items-center justify-between rounded border p-4">
                <div class="flex flex-1 gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-lg">{{ $activity->activity_name }}</span>
                        </div>
                        @if ($activity->description)
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ Str::limit($activity->description, 100) }}
                            </p>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                            <span>📅 {{ $activity->start_date?->format('d/m/Y') }}</span>
                            <span>📍 {{ $activity->location ?? 'Không xác định' }}</span>
                            <span>👥 {{ $activity->registrations_count }} đăng ký</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <flux:button wire:click="openViewModal({{ $activity->id }})" variant="ghost" size="sm">
                        {{ __('Xem') }}
                    </flux:button>
                    <flux:button wire:click="openEditForm({{ $activity->id }})" variant="ghost" size="sm">
                        {{ __('Sửa') }}
                    </flux:button>
                    <flux:button wire:click="openDeleteModal({{ $activity->id }})" variant="danger" size="sm">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="rounded border p-8 text-center text-neutral-500">
                {{ __('Không tìm thấy hoạt động nào.') }}
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 space-y-2">
        {{ $activities->onEachSide(1)->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3"
        on="activity-created">{{ __('Hoạt động đã được thêm thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="activity-updated">{{ __('Hoạt động đã được cập nhật thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="activity-deleted">{{ __('Hoạt động đã được xóa thành công.') }}</x-action-message>

</section>
