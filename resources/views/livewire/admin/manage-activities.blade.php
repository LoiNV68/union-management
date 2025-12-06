@php
    // View rendered by App\Livewire\Admin\ManageActivities
@endphp


<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-orange">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Hoạt động') }}</h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">Tạo và quản lý các hoạt động đoàn</p>
                </div>
            </div>
            <flux:button wire:click="openCreateForm" variant="primary">
                {{ __('Thêm Hoạt động') }}
            </flux:button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm theo tên hoạt động, địa điểm..." type="text" />
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
                             <x-date-picker wire:model="start_date"
                                    :label="__('Ngày bắt đầu')" />
                        </div>
                        <div>
                            <x-date-picker wire:model="end_date"
                                    :label="__('Ngày kết thúc')" />
                        </div>
                    </div>
                    <div>
                        <flux:input wire:model="location" :label="__('Địa điểm')" type="text" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="type" :label="__('Loại hoạt động')" type="text" required />
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

    <!-- Registrations Approval Modal -->
    @if ($showRegistrationsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeRegistrationsModal">
            <div class="w-full max-w-3xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto" wire:click.stop wire:poll.5s>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Duyệt Đăng ký') }}</flux:heading>
                    <flux:button wire:click="closeRegistrationsModal" variant="ghost" size="sm">×</flux:button>
                </div>

                <!-- Pending Registrations -->
                <div class="mb-6">
                    <flux:heading size="md" class="mb-3">{{ __('Đang chờ duyệt') }}</flux:heading>
                    @forelse ($pendingRegistrations as $registration)
                        <div class="mb-3 flex items-center justify-between rounded border p-3 bg-yellow-50 dark:bg-yellow-900/20">
                            <div class="flex-1">
                                <p class="font-semibold">{{ $registration->member->full_name }}</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $registration->member->email }}</p>
                            </div>
                            <div class="flex gap-2">
                                <flux:button wire:click="approveRegistration({{ $registration->id }})"  size="sm">
                                    {{ __('Duyệt') }}
                                </flux:button>
                                <flux:button wire:click="rejectRegistration({{ $registration->id }})" variant="danger" size="sm">
                                    {{ __('Từ chối') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-neutral-500 py-4">{{ __('Không có đơn nào chờ duyệt.') }}</p>
                    @endforelse
                </div>

                <!-- Approved Registrations -->
                <div>
                    <flux:heading size="md" class="mb-3">{{ __('Đã duyệt') }}</flux:heading>
                    @forelse ($approvedRegistrations as $registration)
                        <div class="mb-3 flex items-center justify-between rounded border p-3 bg-green-50 dark:bg-green-900/20">
                            <div class="flex-1">
                                <p class="font-semibold">{{ $registration->member->full_name }}</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $registration->member->email }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:badge variant="success">{{ __('Đã duyệt') }}</flux:badge>
                                <flux:button 
                                    onclick="if(!confirm('{{ __('Bạn có chắc chắn muốn hủy đăng ký này?') }}')) { event.stopImmediatePropagation(); }"
                                    wire:click="cancelRegistration({{ $registration->id }})" 
                                    variant="danger" 
                                    size="sm">
                                    {{ __('Hủy đăng ký') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-neutral-500 py-4">{{ __('Không có đơn đã duyệt.') }}</p>
                    @endforelse
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <flux:button wire:click="closeRegistrationsModal" variant="ghost">
                        {{ __('Đóng') }}
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
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Xác nhận xóa') }}</h3>
                    <p class="text-neutral-600 dark:text-neutral-400">{{ __('Bạn có chắc chắn muốn xóa hoạt động này? Hành động này không thể hoàn tác.') }}</p>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="deleteActivity" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Activities List -->
    <div class="space-y-4" wire:poll.10s.visible>
        @forelse ($activities as $activity)
            <div class="premium-card p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-lg text-neutral-900 dark:text-neutral-100">{{ $activity->activity_name }}</span>
                                @if ($activity->description)
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">{{ Str::limit($activity->description, 100) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                            <span class="inline-flex items-center gap-1">📅 {{ $activity->start_date?->format('d/m/Y') }}</span>
                            <span class="inline-flex items-center gap-1">📍 {{ $activity->location ?? 'Không xác định' }}</span>
                            <span class="inline-flex items-center gap-1">
                                👥 <span class="font-medium text-green-600">{{ $activity->approved_registrations_count }}</span> đã duyệt
                                @if($activity->max_participants)
                                    / {{ $activity->max_participants }}
                                @endif
                                <span class="text-neutral-400">({{ $activity->registrations_count }} tổng)</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <flux:button wire:click="openViewModal({{ $activity->id }})" variant="ghost" size="sm" title="Xem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </flux:button>
                        <flux:button wire:click="openEditForm({{ $activity->id }})" variant="ghost" size="sm" title="Sửa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </flux:button>
                        <flux:button wire:click="openRegistrationsModal({{ $activity->id }})" variant="ghost" size="sm" title="Duyệt đăng ký">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </flux:button>
                        <flux:button wire:click="openDeleteModal({{ $activity->id }})" variant="danger" size="sm" title="Xóa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-neutral-500 font-medium">{{ __('Không tìm thấy hoạt động nào.') }}</p>
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
    <x-action-message class="me-3"
        on="registration-approved">{{ __('Đơn đăng ký đã được duyệt.') }}</x-action-message>
    <x-action-message class="me-3"
        on="registration-rejected">{{ __('Đơn đăng ký đã bị từ chối.') }}</x-action-message>
    <x-action-message class="me-3"
        on="registration-cancelled">{{ __('Đơn đăng ký đã được hủy thành công.') }}</x-action-message>

</section>
