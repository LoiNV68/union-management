@php
    // View rendered by App\Livewire\Union\RegisterActivities
@endphp


<section>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Đăng ký Hoạt động') }}</flux:heading>

    </div>



    <!-- Tabs: Available Activities / Registered Activities -->
    <div class="mb-6 border-b border-neutral-200 dark:border-neutral-700">
        <div class="flex gap-4">
            <button class="px-4 py-2 border-b-2 border-primary-500 font-semibold text-primary-600 dark:text-primary-400">
                {{ __('Hoạt động Khả dụng') }}
            </button>
        </div>
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
            </flux:select>
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
    <div class="mb-6" wire:poll.5s.visible>
        <flux:heading size="md" class="mb-4">{{ __('Các hoạt động sắp tới') }}</flux:heading>

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
                                <span>👥 {{ $activity->approved_registrations_count }}
                                    @if ($activity->max_participants)
                                        / {{ $activity->max_participants }} ({{ $activity->registrations_count }} đăng ký)
                                    @else
                                        đã duyệt ({{ $activity->registrations_count }} đăng ký)
                                    @endif
                                </span>
                                @if($activity->max_participants && $activity->approved_registrations_count >= $activity->max_participants)
                                    <flux:badge variant="danger">{{ __('Đã đủ') }}</flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button wire:click="openActivityModal({{ $activity->id }})" variant="primary"
                            size="sm">
                            {{ __('Xem & Đăng ký') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="rounded border p-8 text-center text-neutral-500">
                    {{ __('Không có hoạt động nào.') }}
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4 space-y-2">
            {{ $activities->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Registered Activities Section -->
    @if ($registeredActivities->count() > 0)
        <div>
            <flux:heading size="md" class="mb-4">{{ __('Hoạt động đã đăng ký') }}</flux:heading>

            <div class="grid gap-2">
                @foreach ($registeredActivities as $registration)
                    @php
                        $statusClass = match($registration->registration_status) {
                            0 => 'border-yellow-200 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20',
                            1 => 'border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20',
                            2 => 'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20',
                            default => ''
                        };
                        $statusBadge = match($registration->registration_status) {
                            0 => ['label' => __('Chờ duyệt'), 'variant' => 'warning'],
                            1 => ['label' => __('Đã duyệt'), 'variant' => 'success'],
                            2 => ['label' => __('Bị từ chối'), 'variant' => 'danger'],
                            default => ['label' => __('Không xác định'), 'variant' => 'neutral']
                        };
                    @endphp
                    <div class="flex items-center justify-between rounded border p-4 {{ $statusClass }}">
                        <div class="flex flex-1 gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-lg">{{ $registration->activity->activity_name }}</span>
                                    <flux:badge :variant="$statusBadge['variant']">{{ $statusBadge['label'] }}</flux:badge>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                                    <span>📅 {{ $registration->activity->start_date?->format('d/m/Y') }}</span>
                                    <span>📍 {{ $registration->activity->location ?? 'Không xác định' }}</span>
                                    <span>🕒 {{ __('Đăng ký lúc: ') }} {{ $registration->registration_time }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($registration->registration_status !== 2)
                            <flux:button
                                onclick="if(!confirm('{{ __('Bạn có chắc chắn muốn hủy đăng ký?') }}')) { event.stopImmediatePropagation(); }"
                                wire:click="cancelRegistration({{ $registration->id }})" variant="danger" size="sm">
                                {{ __('Hủy đăng ký') }}
                            </flux:button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Messages -->
    <x-action-message class="me-3"
        on="activity-registered">{{ __('Đã đăng ký hoạt động thành công. Vui lòng chờ duyệt từ quản trị viên.') }}</x-action-message>
    <x-action-message class="me-3"
        on="activity-cancelled">{{ __('Đã hủy đăng ký hoạt động thành công.') }}</x-action-message>
</section>
