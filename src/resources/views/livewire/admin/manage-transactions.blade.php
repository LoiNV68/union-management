<section>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Thu Chi') }}</flux:heading>
        <flux:button wire:click="openCreateForm" variant="primary">
            {{ __('Thêm Khoản Thu/Chi') }}
        </flux:button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label="" placeholder="Tìm kiếm theo tiêu đề..."
                type="text" />
        </div>
        <div class="w-40">
            <flux:select wire:model.live="perPage" label="">
                <option value="5">5 / trang</option>
                <option value="10">10 / trang</option>
                <option value="20">20 / trang</option>
            </flux:select>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <flux:heading size="lg" class="mb-4">{{ __('Xác nhận xóa') }}</flux:heading>
                <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                    {{ __('Bạn có chắc chắn muốn xóa khoản thu chi này?') }}
                </p>
                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="deleteTransaction" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Transactions List -->
    <div class="grid gap-2">
        @forelse ($transactions as $transaction)
            <div class="flex items-center justify-between rounded border p-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-lg">{{ $transaction->title }}</span>
                        <flux:badge :variant="$transaction->type == 0 ? 'success' : 'danger'">
                            {{ $transaction->type == 0 ? 'Thu' : 'Chi' }}
                        </flux:badge>
                        @if ($transaction->status == 1)
                            <flux:badge variant="neutral">Đã đóng</flux:badge>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Số tiền: {{ number_format($transaction->amount, 0, ',', '.') }} VNĐ |
                        Đã thanh toán: {{ $transaction->paid_count }}/{{ $transaction->total_members }}
                        @if ($transaction->due_date)
                            | Hạn: {{ $transaction->due_date->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <flux:button wire:click="openViewModal({{ $transaction->id }})" variant="ghost" size="sm">
                        {{ __('Xem') }}
                    </flux:button>
                    @if ($transaction->status == 0)
                        <flux:button wire:click="openEditForm({{ $transaction->id }})" variant="ghost" size="sm">
                            {{ __('Sửa') }}
                        </flux:button>
                        <flux:button wire:click="closeTransaction({{ $transaction->id }})" variant="ghost" size="sm">
                            {{ __('Đóng') }}
                        </flux:button>
                    @endif
                    <flux:button wire:click="openDeleteModal({{ $transaction->id }})" variant="danger" size="sm">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="rounded border p-8 text-center text-neutral-500">
                {{ __('Chưa có khoản thu chi nào.') }}
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