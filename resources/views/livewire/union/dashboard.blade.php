<x-layouts.app :title="__('Bảng Điều Khiển')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @if(isset($error))
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-900 dark:bg-red-900/20 p-6">
                <p class="text-red-600 dark:text-red-400">{{ $error }}</p>
            </div>
        @else
            <!-- Header -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">
                    Xin chào, {{ $member->full_name }}!
                </h1>
                <p class="text-neutral-600 dark:text-neutral-400">
                    Mã sinh viên: {{ $member->user?->student_code }} | Chi đoàn:
                    {{ $member->branch?->branch_name ?? 'N/A' }}
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Activities Card -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Hoạt động</p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalRegistrations }}</p>
                            <p class="text-xs text-neutral-500 mt-1">
                                {{ $approvedRegistrations }} đã duyệt / {{ $pendingRegistrations }} chờ
                            </p>
                        </div>
                        <div class="rounded-full bg-purple-100 dark:bg-purple-900/20 p-3">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Payments Card -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Khoản thu</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalTransactions }}</p>
                            <p class="text-xs text-neutral-500 mt-1">
                                {{ $paidTransactions }} đã trả / {{ $pendingPayments }} chưa
                            </p>
                        </div>
                        <div class="rounded-full bg-green-100 dark:bg-green-900/20 p-3">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Training Points Average -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Điểm TB</p>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                                {{ number_format($avgPoints, 2) }}
                            </p>
                            <p class="text-xs text-neutral-500 mt-1">
                                {{ $trainingPoints->count() }} học kỳ
                            </p>
                        </div>
                        <div class="rounded-full bg-orange-100 dark:bg-orange-900/20 p-3">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Latest Training Point -->
                @if($latestPoint)
                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">Điểm mới nhất</p>
                                <p class="text-3xl font-bold
                                                    {{ $latestPoint->point >= 90 ? 'text-green-600 dark:text-green-400' : '' }}
                                                    {{ $latestPoint->point >= 80 && $latestPoint->point < 90 ? 'text-blue-600 dark:text-blue-400' : '' }}
                                                    {{ $latestPoint->point >= 65 && $latestPoint->point < 80 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                                    {{ $latestPoint->point < 65 ? 'text-red-600 dark:text-red-400' : '' }}">
                                    {{ number_format($latestPoint->point, 2) }}
                                </p>
                                <p class="text-xs text-neutral-500 mt-1">
                                    {{ $latestPoint->semester->school_year }} - HK{{ $latestPoint->semester->semester }}
                                </p>
                            </div>
                            <div class="rounded-full 
                                                {{ $latestPoint->point >= 90 ? 'bg-green-100 dark:bg-green-900/20' : '' }}
                                                {{ $latestPoint->point >= 80 && $latestPoint->point < 90 ? 'bg-blue-100 dark:bg-blue-900/20' : '' }}
                                                {{ $latestPoint->point >= 65 && $latestPoint->point < 80 ? 'bg-yellow-100 dark:bg-yellow-900/20' : '' }}
                                                {{ $latestPoint->point < 65 ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                                                p-3">
                                <svg class="w-8 h-8
                                                    {{ $latestPoint->point >= 90 ? 'text-green-600 dark:text-green-400' : '' }}
                                                    {{ $latestPoint->point >= 80 && $latestPoint->point < 90 ? 'text-blue-600 dark:text-blue-400' : '' }}
                                                    {{ $latestPoint->point >= 65 && $latestPoint->point < 80 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                                    {{ $latestPoint->point < 65 ? 'text-red-600 dark:text-red-400' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                        <div class="flex items-center justify-center h-full">
                            <p class="text-sm text-neutral-500">Chưa có điểm rèn luyện</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Charts Row -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Financial Summary -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                        Tình hình tài chính
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Tổng khoản thu</span>
                            <span class="text-sm font-semibold">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Đã thanh toán</span>
                            <span
                                class="text-sm font-semibold text-green-600">{{ number_format($paidAmount, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="border-t border-neutral-200 dark:border-neutral-700 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Còn phải trả</span>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                    {{ number_format($totalAmount - $paidAmount, 0, ',', '.') }}₫
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Training Points Chart -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                        Điểm rèn luyện theo học kỳ
                    </h3>
                    @if($trainingPoints->count() > 0)
                        <div class="space-y-2">
                            @foreach($trainingPoints->take(5) as $tp)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $tp->semester->school_year }} - HK{{ $tp->semester->semester }}
                                    </span>
                                    <span class="text-sm font-semibold
                                                                {{ $tp->point >= 90 ? 'text-green-600' : '' }}
                                                                {{ $tp->point >= 80 && $tp->point < 90 ? 'text-blue-600' : '' }}
                                                                {{ $tp->point >= 65 && $tp->point < 80 ? 'text-yellow-600' : '' }}
                                                                {{ $tp->point < 65 ? 'text-red-600' : '' }}">
                                        {{ number_format($tp->point, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-neutral-500 text-center py-4">Chưa có điểm rèn luyện</p>
                    @endif
                </div>
            </div>

            <!-- Recent Registrations -->
            @if($recentRegistrations->count() > 0)
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                        Hoạt động đã đăng ký gần đây
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentRegistrations as $reg)
                            <div
                                class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3 last:border-0">
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $reg->activity->activity_name }}</p>
                                    <p class="text-xs text-neutral-500">
                                        {{ \Carbon\Carbon::parse($reg->activity->start_date)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <flux:badge :variant="$reg->status == 1 ? 'success' : ($reg->status == 0 ? 'warning' : 'danger')">
                                    {{ $reg->status == 1 ? 'Đã duyệt' : ($reg->status == 0 ? 'Chờ duyệt' : 'Từ chối') }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>