<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="icon-gradient-green">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Khoản Thu') }}</h1>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">Xem và thanh toán các khoản phí</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="premium-card p-4 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Tìm kiếm..." type="text" />
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
                            {{ number_format($viewingTransaction->amount_per_member, 0, ',', '.') }} ₫
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
    <div class="space-y-4">
        @forelse ($memberTransactions as $mt)
            @php
                $cardClass = match ($mt->payment_status) {
                    0 => 'border-l-4 border-l-yellow-500',
                    1 => 'border-l-4 border-l-blue-500',
                    2 => 'border-l-4 border-l-green-500',
                    default => '',
                };
                $statusBadge = match ($mt->payment_status) {
                    0 => ['label' => __('Chưa thanh toán'), 'variant' => 'warning', 'icon' => '⚠️'],
                    1 => ['label' => __('Chờ xác nhận'), 'variant' => 'neutral', 'icon' => '⏳'],
                    2 => ['label' => __('Đã xác nhận'), 'variant' => 'success', 'icon' => '✅'],
                    default => ['label' => __('Không xác định'), 'variant' => 'neutral', 'icon' => '❓'],
                };
            @endphp
            <div class="premium-card p-5 {{ $cardClass }}">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="font-bold text-lg text-neutral-900 dark:text-neutral-100">{{ $mt->transaction->title }}</span>
                            <flux:badge :variant="$statusBadge['variant']">{{ $statusBadge['icon'] }}
                                {{ $statusBadge['label'] }}
                            </flux:badge>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                            <span class="inline-flex items-center gap-1">
                                💰 <span
                                    class="font-semibold text-red-600">{{ number_format($mt->amount_per_member, 0, ',', '.') }}
                                    VNĐ</span>
                            </span>
                            @if ($mt->transaction->due_date)
                                <span class="inline-flex items-center gap-1">📅 Hạn:
                                    {{ $mt->transaction->due_date->format('d/m/Y') }}</span>
                            @endif
                            @if ($mt->payment_date)
                                <span class="inline-flex items-center gap-1">✅ Thanh toán:
                                    {{ $mt->payment_date->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                        @if ($mt->transaction->description)
                            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ $mt->transaction->description }}
                            </p>
                        @endif
                    </div>
                    @if ($mt->payment_status < 2)
                        <flux:button wire:click="openQrModal({{ $mt->id }})" variant="primary" size="sm">
                            💳 {{ __('Thanh toán') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @empty
            <div class="premium-card p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <p class="text-neutral-500 font-medium">{{ __('Không có khoản thu nào.') }}</p>
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