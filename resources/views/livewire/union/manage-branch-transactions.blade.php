<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="icon-gradient-pink">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Quản lý Thu Chi Chi Đoàn') }}
                    </h1>
                    <p class="text-neutral-600 dark:text-neutral-400 text-sm">
                        Quản lý các khoản thu chi của chi đoàn bạn
                        @if($userBranches->count() > 0)
                            ({{ $userBranches->pluck('branch_name')->join(', ') }})
                        @endif
                    </p>
                </div>
            </div>
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

    <!-- View Members Modal -->
    @if ($showViewModal && $viewingTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeViewModal">
            <div class="w-full max-w-4xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $viewingTransaction->title }}</flux:heading>
                        @if($viewingTransaction->type == 0)
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ __('Đã thanh toán:') }} {{ $viewingTransaction->paid_count ?? 0 }} /
                                {{ $viewingTransaction->total_members ?? $viewingTransaction->filteredMemberTransactions->count() }}
                            </p>
                        @else
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                Khoản chi: {{ number_format($viewingTransaction->amount, 0, ',', '.') }} VNĐ
                            </p>
                            @if($viewingTransaction->description)
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                                    {{ $viewingTransaction->description }}
                                </p>
                            @endif
                        @endif
                    </div>
                    <flux:button wire:click="closeViewModal" variant="ghost" size="sm">×</flux:button>
                </div>

                @if($viewingTransaction->type == 0)
                    <div class="mb-4">
                        <flux:input wire:model.live.debounce.300ms="memberSearch"
                            placeholder="🔍 Tìm theo tên sinh viên hoặc mã SV..." type="text" />
                    </div>

                    <div class="space-y-3">
                        @forelse ($viewingTransaction->filteredMemberTransactions as $mt)
                            <div
                                class="flex items-center justify-between rounded border p-3 {{ $mt->payment_status >= 1 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-neutral-50 dark:bg-neutral-900' }}">
                                <div class="flex-1">
                                    <p class="font-semibold">{{ $mt->member->full_name }}</p>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $mt->member->user?->student_code }} - {{ $mt->member->branch->branch_name }}
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
                        @empty
                            <div class="p-8 text-center text-neutral-500">
                                {{ __('Không tìm thấy sinh viên nào.') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            @if(!empty($branchesPaymentStatus))
                                @foreach($branchesPaymentStatus as $status)
                                    <flux:button wire:click="openPayConfirmModal({{ $viewingTransaction->id }})" variant="primary" class="mb-2">
                                        💰 Thanh toán cho quản lý - {{ $status['branch']->branch_name }}
                                        @if($status['all_paid'])
                                            (Đã thu đủ)
                                        @else
                                            ({{ $status['paid_count'] }}/{{ $status['total_count'] }})
                                        @endif
                                    </flux:button>
                                @endforeach
                            @endif
                        </div>
                        <flux:button wire:click="closeViewModal" variant="ghost">
                            {{ __('Đóng') }}
                        </flux:button>
                    </div>
                @else
                    <div class="mt-6 flex items-center justify-end">
                        <flux:button wire:click="closeViewModal" variant="ghost">
                            {{ __('Đóng') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Pay to Management Confirm Modal -->
    @if ($showPayConfirmModal && $payingTransactionId)
        @php
            $payingTransaction = \App\Models\Transaction::find($payingTransactionId);
        @endphp
        @if($payingTransaction && !empty($branchesPaymentStatus))
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closePayConfirmModal">
                <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">Xác nhận thanh toán cho quản lý</flux:heading>
                        <flux:button wire:click="closePayConfirmModal" variant="ghost" size="sm">×</flux:button>
                    </div>

                    <div class="mb-4">
                        <p class="text-neutral-600 dark:text-neutral-400 mb-2">
                            Bạn có chắc chắn muốn tạo khoản chi cho quản lý từ khoản thu:
                        </p>
                        <p class="font-bold text-lg">{{ $payingTransaction->title }}</p>
                        
                        <div class="mt-4 space-y-2">
                            @foreach($branchesPaymentStatus as $status)
                                @php
                                    $totalMembersCount = $payingTransaction->memberTransactions()->count();
                                    $amountPerMember = $totalMembersCount > 0 ? $payingTransaction->amount / $totalMembersCount : 0;
                                    // Số tiền thu của chi đoàn = số tiền mỗi thành viên * số thành viên trong chi đoàn
                                    $totalAmount = $amountPerMember * $status['total_count'];
                                @endphp
                                <div class="p-3 bg-neutral-50 dark:bg-neutral-900 rounded">
                                    <p class="font-semibold">{{ $status['branch']->branch_name }}</p>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                        Số thành viên: {{ $status['total_count'] }} (Đã thu: {{ $status['paid_count'] }})
                                    </p>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                        Số tiền mỗi thành viên: {{ number_format($amountPerMember, 0, ',', '.') }} VNĐ
                                    </p>
                                    <p class="text-sm font-semibold text-green-600 mt-1">
                                        Tổng số tiền chi đoàn: {{ number_format($totalAmount, 0, ',', '.') }} VNĐ
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <flux:button wire:click="closePayConfirmModal" variant="ghost" type="button">
                            {{ __('Hủy') }}
                        </flux:button>
                        <flux:button wire:click="confirmPayToManagement" variant="primary" type="button">
                            {{ __('Xác nhận') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Transactions List -->
    <div class="space-y-4">
        @forelse ($transactions as $transaction)
            <div class="premium-card p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl {{ $transaction->type == 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center">
                                @if($transaction->type == 0)
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-bold text-lg text-neutral-900 dark:text-neutral-100">{{ $transaction->title }}</span>
                                    <flux:badge :variant="$transaction->type == 0 ? 'success' : 'danger'">
                                        {{ $transaction->type == 0 ? 'Thu' : 'Chi' }}
                                    </flux:badge>
                                    @if ($transaction->status == 1)
                                        <flux:badge variant="neutral">🔒 Đã đóng</flux:badge>
                                    @endif
                                </div>
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                                    <span class="inline-flex items-center gap-1">
                                        💰 <span
                                            class="font-semibold {{ $transaction->type == 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($transaction->amount, 0, ',', '.') }}
                                            VNĐ</span>
                                    </span>
                                    @if($transaction->type == 0)
                                        <span class="inline-flex items-center gap-1">
                                            ✅ <span
                                                class="font-medium">{{ $transaction->paid_count }}</span>/{{ $transaction->total_members }}
                                            đã thanh toán
                                        </span>
                                    @endif
                                    @if ($transaction->due_date)
                                        <span class="inline-flex items-center gap-1">📅 Hạn:
                                            {{ $transaction->due_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                @if ($transaction->description)
                                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $transaction->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <flux:button wire:click="openViewModal({{ $transaction->id }})" variant="primary" size="sm"
                            title="Xem chi tiết">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Xem chi tiết
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-neutral-500 font-medium">{{ __('Chưa có khoản thu chi nào.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</section>

