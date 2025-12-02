<section>
    <div class="mb-6">
        <flux:heading size="lg">{{ __('Khoản Thu') }}</flux:heading>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label="" placeholder="Tìm kiếm..." type="text" />
        </div>
        <div class="w-40">
            <flux:select wire:model.live="perPage" label="">
                <option value="5">5 / trang</option>
                <option value="10">10 / trang</option>
                <option value="20">20 / trang</option>
            </flux:select>
        </div>
    </div>

    <!-- Modal QR -->
    @if($showQrModal && $viewingTransaction)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 relative">
                <button wire:click="closeQrModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="text-xl font-bold text-center mb-4">Thanh toán khoản phí</h3>

                <div class="text-center space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-semibold text-lg">{{ $viewingTransaction->transaction->title }}</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ number_format($viewingTransaction->transaction->amount) }} ₫
                        </p>
                        <p class="text-sm text-gray-600 mt-2">
                            Mã SV: {{ $viewingTransaction->member->user?->student_code ?? 'N/A' }}
                        </p>
                    </div>

                    @if($qrCode)
                        <img src="{{ $qrCode }}" alt="QR Code" class="mx-auto border-8 border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-600">Quét bằng <strong>bất kỳ app ngân hàng nào</strong></p>
                    @else
                        <p class="text-red-500">Đang tạo QR...</p>
                    @endif

                    <!-- Nút giả lập thanh toán (rất quan trọng khi demo) -->
                    <div class="mt-6 space-y-3">
                        <!-- <button wire:click="fakePaymentSuccess"
                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg transform transition hover:scale-105">
                                    Giả lập thanh toán thành công (Demo)
                                </button> -->

                        <button wire:click="markAsPaid"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg">
                            Tôi đã chuyển khoản (thật)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Transactions List -->
    <div class="grid gap-2">
        @forelse ($memberTransactions as $mt)
            @php
                $statusClass = match ($mt->payment_status) {
                    0 => 'border-yellow-200 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20',
                    1 => 'border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/20',
                    2 => 'border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20',
                    default => '',
                };
                $statusBadge = match ($mt->payment_status) {
                    0 => ['label' => __('Chưa thanh toán'), 'variant' => 'warning'],
                    1 => ['label' => __('Chờ xác nhận'), 'variant' => 'neutral'],
                    2 => ['label' => __('Đã xác nhận'), 'variant' => 'success'],
                    default => ['label' => __('Không xác định'), 'variant' => 'neutral'],
                };
            @endphp
            <div class="flex items-center justify-between rounded border p-4 {{ $statusClass }}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-lg">{{ $mt->transaction->title }}</span>
                        <flux:badge :variant="$statusBadge['variant']">{{ $statusBadge['label'] }}</flux:badge>
                    </div>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Số tiền: {{ number_format($mt->transaction->amount, 0, ',', '.') }} VNĐ
                        @if ($mt->transaction->due_date)
                            | Hạn: {{ $mt->transaction->due_date->format('d/m/Y') }}
                        @endif
                        @if ($mt->payment_date)
                            | Thanh toán: {{ $mt->payment_date->format('d/m/Y H:i') }}
                        @endif
                    </p>
                    @if ($mt->transaction->description)
                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $mt->transaction->description }}
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if ($mt->payment_status < 2)
                        <flux:button wire:click="openQrModal({{ $mt->id }})" variant="primary" size="sm">
                            {{ __('Thanh toán') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded border p-8 text-center text-neutral-500">
                {{ __('Không có khoản thu nào.') }}
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $memberTransactions->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3"
        on="payment-marked">{{ __('Đã đánh dấu thanh toán. Chờ xác nhận từ quản trị viên.') }}
    </x-action-message>
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (event) => {
                const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-coin-win-notification-1939.mp3');
                audio.play();
                // Dùng toast library bạn đang có (hoặc alert tạm)
                alert(event.message ?? 'Thao tác thành công!');
            });
        });
    </script>
@endpush