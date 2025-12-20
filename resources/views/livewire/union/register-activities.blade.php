@php
    // View rendered by App\Livewire\Union\RegisterActivities
@endphp


<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="icon-gradient-green">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Đăng ký Hoạt động') }}</h1>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">Xem và đăng ký tham gia các hoạt động đoàn</p>
            </div>
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

    <!-- Activity Details Modal -->
    @if ($showActivityModal && $viewingActivity)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeActivityModal">
            <div class="w-full max-w-2xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl max-h-[90vh] overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ $viewingActivity->activity_name }}</flux:heading>
                    <flux:button wire:click="closeActivityModal" variant="ghost" size="sm">×</flux:button>
                </div>

                <div class="space-y-4 mb-6">
                    @if ($viewingActivity->description)
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Mô tả</p>
                            <p class="text-lg">{{ $viewingActivity->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
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
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Số đã được duyệt</p>
                            <p class="text-lg">{{ $viewingActivity->approved_registrations_count }}
                                @if($viewingActivity->max_participants)
                                    / {{ $viewingActivity->max_participants }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Tổng đăng ký</p>
                            <p class="text-lg">{{ $viewingActivity->registrations_count }}</p>
                        </div>
                    </div>
                </div>

                @error('register')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeActivityModal" variant="ghost">
                        {{ __('Đóng') }}
                    </flux:button>
                    @php
                        $isFull = $viewingActivity->max_participants && 
                                  $viewingActivity->approved_registrations_count >= $viewingActivity->max_participants;
                    @endphp
                    <flux:button 
                        wire:click="registerActivity({{ $viewingActivity->id }})" 
                        variant="primary"
                        :disabled="$isFull">
                        {{ $isFull ? __('Đã đủ số lượng') : __('Đăng ký tham gia') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Activities List -->
    <div class="mb-8" wire:poll.5s.visible>
        <div class="flex items-center gap-2 mb-4">
            <div class="icon-gradient-orange">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ __('Các hoạt động sắp tới') }}</h2>
        </div>

        <div class="space-y-4">
            @forelse ($activities as $activity)
                <div class="premium-card p-5 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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
                                    👥 <span class="font-medium text-green-600">{{ $activity->approved_registrations_count }}</span>
                                    @if ($activity->max_participants)
                                        / {{ $activity->max_participants }}
                                    @endif
                                    <span class="text-neutral-400">({{ $activity->registrations_count }} đăng ký)</span>
                                </span>
                                @if($activity->max_participants && $activity->approved_registrations_count >= $activity->max_participants)
                                    <flux:badge variant="danger">🚫 {{ __('Đã đủ') }}</flux:badge>
                                @endif
                            </div>
                        </div>
                        <flux:button wire:click="openActivityModal({{ $activity->id }})" variant="primary" size="sm">
                            {{ __('Xem & Đăng ký') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="premium-card p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-neutral-500 font-medium">{{ __('Không có hoạt động nào.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $activities->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Registered Activities Section -->
    @if ($registeredActivities->count() > 0)
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="icon-gradient-blue">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ __('Hoạt động đã đăng ký') }}</h2>
            </div>

            <div class="space-y-3">
                @foreach ($registeredActivities as $registration)
                    @php
                        $cardClass = match($registration->registration_status) {
                            0 => 'border-l-4 border-l-yellow-500',
                            1 => 'border-l-4 border-l-green-500',
                            2 => 'border-l-4 border-l-red-500',
                            default => ''
                        };
                        $statusBadge = match($registration->registration_status) {
                            0 => ['label' => __('Chờ duyệt'), 'variant' => 'warning', 'icon' => '⏳'],
                            1 => ['label' => __('Đã duyệt'), 'variant' => 'success', 'icon' => '✅'],
                            2 => ['label' => __('Bị từ chối'), 'variant' => 'danger', 'icon' => '❌'],
                            default => ['label' => __('Không xác định'), 'variant' => 'neutral', 'icon' => '❓']
                        };
                    @endphp
                    <div class="premium-card p-4 {{ $cardClass }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $registration->activity->activity_name }}</span>
                                    <flux:badge :variant="$statusBadge['variant']">{{ $statusBadge['icon'] }} {{ $statusBadge['label'] }}</flux:badge>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                                    <span>📅 {{ $registration->activity->start_date?->format('d/m/Y') }}</span>
                                    <span>📍 {{ $registration->activity->location ?? 'Không xác định' }}</span>
                                    <span>🕒 {{ __('Đăng ký lúc: ') }} {{ $registration->registration_time }}</span>
                                </div>
                            </div>
                            @if ($registration->registration_status !== 2)
                                <flux:button
                                    wire:key="cancel-btn-{{ $registration->id }}"
                                    wire:click="openCancelModal({{ $registration->id }})" variant="danger" size="sm">
                                    {{ __('Hủy đăng ký') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cancellation Confirmation Modal -->
    @if ($showCancelModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50" wire:click="closeCancelModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="size-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Xác nhận hủy đăng ký') }}</h3>
                    <p class="text-neutral-600 dark:text-neutral-400">
                        {{ __('Bạn có chắc chắn muốn hủy đăng ký hoạt động này?') }}
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <flux:button wire:click="closeCancelModal" variant="ghost">{{ __('Hủy bỏ') }}</flux:button>
                    <flux:button wire:click="cancelRegistration({{ $cancellingId }})" variant="danger">{{ __('Xác nhận hủy') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Messages -->
    <x-action-message class="me-3"
        on="activity-registered">{{ __('Đã đăng ký hoạt động thành công. Vui lòng chờ duyệt từ quản trị viên.') }}</x-action-message>
    <x-action-message class="me-3"
        on="activity-cancelled">{{ __('Đã hủy đăng ký hoạt động thành công.') }}</x-action-message>
</section>
