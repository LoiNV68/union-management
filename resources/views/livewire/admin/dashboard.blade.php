<x-layouts.app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">
                Bảng Điều Khiển Quản Trị
            </h1>
            <p class="text-neutral-600 dark:text-neutral-400">
                Tổng quan hệ thống quản lý đoàn viên
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Members Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Thành viên</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalMembers }}</p>
                        <p class="text-xs text-neutral-500 mt-1">
                            <span class="text-green-600">{{ $activeMembers }} hoạt động</span> /
                            <span class="text-red-600">{{ $inactiveMembers }} ngừng</span>
                        </p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/20 p-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Activities Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Hoạt động</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $totalActivities }}</p>
                        <p class="text-xs text-neutral-500 mt-1">
                            {{ $upcomingActivities }} sắp tới / {{ $completedActivities }} hoàn thành
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

            <!-- Finance Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Thu chi</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}₫
                        </p>
                        <p class="text-xs text-neutral-500 mt-1">
                            Tỷ lệ thanh toán: {{ $paymentRate }}%
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

            <!-- Training Points Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Điểm rèn luyện TB</p>
                        <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($avgTrainingPoint, 2) }}
                        </p>
                        <p class="text-xs text-neutral-500 mt-1">
                            Tổng: {{ $totalTrainingPoints }} điểm
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
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Training Points Distribution -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                    Phân loại điểm rèn luyện
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Xuất sắc (≥90)</span>
                        </div>
                        <span class="text-sm font-semibold">{{ $trainingPointsDistribution['excellent'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Tốt (80-89)</span>
                        </div>
                        <span class="text-sm font-semibold">{{ $trainingPointsDistribution['good'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Khá (65-79)</span>
                        </div>
                        <span class="text-sm font-semibold">{{ $trainingPointsDistribution['average'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Trung bình (<65)< /span>
                        </div>
                        <span class="text-sm font-semibold">{{ $trainingPointsDistribution['below'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                    Tổng quan tài chính
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Tổng thu</span>
                        <span
                            class="text-sm font-semibold text-green-600">+{{ number_format($totalRevenue, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Tổng chi</span>
                        <span
                            class="text-sm font-semibold text-red-600">-{{ number_format($totalExpense, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="border-t border-neutral-200 dark:border-neutral-700 pt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Còn lại</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>
                    <div class="border-t border-neutral-200 dark:border-neutral-700 pt-2 mt-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-600 dark:text-neutral-400">Đã thanh toán</span>
                            <span class="font-semibold">{{ $paidTransactions }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-neutral-600 dark:text-neutral-400">Chờ thanh toán</span>
                            <span class="font-semibold">{{ $pendingTransactions }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                Hoạt động gần đây
            </h3>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                    <div
                        class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3 last:border-0">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activity->activity_name }}</p>
                            <p class="text-xs text-neutral-500">
                                {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y') }} -
                                {{ $activity->location }}
                            </p>
                        </div>
                        <flux:badge :variant="$activity->start_date > now() ? 'primary' : 'neutral'">
                            {{ $activity->start_date > now() ? 'Sắp diễn ra' : 'Đã diễn ra' }}
                        </flux:badge>
                    </div>
                @empty
                    <p class="text-neutral-500 text-center py-4">Chưa có hoạt động nào</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>