<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-pink">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Thu Chi') }}</h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">Quản lý các khoản thu chi của đoàn</p>
                </div>
            </div>
            <flux:button wire:click="openCreateForm" variant="primary">
                {{ __('Thêm Khoản Thu/Chi') }}
            </flux:button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm theo tiêu đề..." type="text" />
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateForm">
            <div class="w-full max-w-2xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $editingId ? __('Sửa Khoản Thu/Chi') : __('Thêm Khoản Thu/Chi Mới') }}
                    </flux:heading>
                    <flux:button wire:click="closeCreateForm" variant="ghost" size="sm">×</flux:button>
                </div>

                <form wire:submit="saveTransaction" class="space-y-4">
                    <div>
                        <flux:input wire:model="title" :label="__('Tiêu đề')" type="text" required />
                    </div>
                    <div>
                        <flux:textarea wire:model="description" :label="__('Mô tả')" rows="3" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:input wire:model="amount" :label="__('Số tiền (VNĐ)')" type="number" step="1000" min="0"
                                required />
                        </div>
                        <div>
                            <flux:select wire:model="type" :label="__('Loại')" required>
                                <option value="0">Thu</option>
                                <option value="1">Chi</option>
                            </flux:select>
                        </div>
                    </div>
                    <div>
                        <x-date-picker wire:model="due_date" :label="__('Hạn thanh toán')" />
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

    <!-- View Members Modal -->
    @if ($showViewModal && $viewingTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeViewModal">
            <div class="w-full max-w-4xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $viewingTransaction->title }}</flux:heading>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Đã thanh toán:') }} {{ $viewingTransaction->paid_count }} /
                            {{ $viewingTransaction->member_transactions_count }}
                        </p>
                    </div>
                    <flux:button wire:click="closeViewModal" variant="ghost" size="sm">×</flux:button>
                </div>

                <div class="space-y-3">
                    @foreach ($viewingTransaction->memberTransactions as $mt)
                        <div
                            class="flex items-center justify-between rounded border p-3 {{ $mt->payment_status >= 1 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-neutral-50 dark:bg-neutral-900' }}">
                            <div class="flex-1">
                                <p class="font-semibold">{{ $mt->member->full_name }}</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $mt->member->user?->student_code }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($mt->payment_status == 0)
                                    <flux:badge variant="neutral">Chưa thanh toán</flux:badge>
                                @elseif($mt->payment_status == 1)
                                    <flux:badge variant="warning">Chờ xác nhận</flux:badge>
                                    <flux:button wire:click="confirmPayment({{ $mt->id }})" size="sm">
                                        Xác nhận
                                    </flux:button>
                                @else
                                    <flux:badge variant="success">Đã xác nhận</flux:badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <flux:button wire:click="closeViewModal" variant="ghost">
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
                    <p class="text-neutral-600 dark:text-neutral-400">{{ __('Bạn có chắc chắn muốn xóa khoản thu chi này?') }}</p>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="deleteTransaction" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Transactions List -->
    <div class="space-y-4">
        @forelse ($transactions as $transaction)
            <div class="premium-card p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $transaction->type == 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center">
                                @if($transaction->type == 0)
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-lg text-neutral-900 dark:text-neutral-100">{{ $transaction->title }}</span>
                                    <flux:badge :variant="$transaction->type == 0 ? 'success' : 'danger'">
                                        {{ $transaction->type == 0 ? 'Thu' : 'Chi' }}
                                    </flux:badge>
                                    @if ($transaction->status == 1)
                                        <flux:badge variant="neutral">🔒 Đã đóng</flux:badge>
                                    @endif
                                </div>
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                                    <span class="inline-flex items-center gap-1">
                                        💰 <span class="font-semibold {{ $transaction->type == 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        ✅ <span class="font-medium">{{ $transaction->paid_count }}</span>/{{ $transaction->total_members }} đã thanh toán
                                    </span>
                                    @if ($transaction->due_date)
                                        <span class="inline-flex items-center gap-1">📅 Hạn: {{ $transaction->due_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <flux:button wire:click="openViewModal({{ $transaction->id }})" variant="ghost" size="sm" title="Xem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </flux:button>
                        @if ($transaction->status == 0)
                            <flux:button wire:click="openEditForm({{ $transaction->id }})" variant="ghost" size="sm" title="Sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </flux:button>
                            <flux:button wire:click="closeTransaction({{ $transaction->id }})" variant="ghost" size="sm" title="Đóng">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </flux:button>
                        @endif
                        <flux:button wire:click="openDeleteModal({{ $transaction->id }})" variant="danger" size="sm" title="Xóa">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-neutral-500 font-medium">{{ __('Chưa có khoản thu chi nào.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="transaction-created">{{ __('Đã tạo khoản thu chi.') }}</x-action-message>
    <x-action-message class="me-3" on="transaction-updated">{{ __('Đã cập nhật.') }}</x-action-message>
    <x-action-message class="me-3" on="transaction-deleted">{{ __('Đã xóa.') }}</x-action-message>
    <x-action-message class="me-3" on="notification-sent">{{ __('Đã gửi thông báo.') }}</x-action-message>
    <x-action-message class="me-3" on="payment-confirmed">{{ __('Đã xác nhận thanh toán.') }}</x-action-message>
    <x-action-message class="me-3" on="transaction-closed">{{ __('Đã đóng khoản thu chi.') }}</x-action-message>
</section>