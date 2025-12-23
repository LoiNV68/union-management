<x-layouts.app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header with Gradient -->
        <div class="premium-card p-6 relative overflow-hidden">
            <div class="absolute inset-0 gradient-primary opacity-5"></div>
            <div class="relative">
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">
                    🎛️ Bảng Điều Khiển Quản Trị
                </h1>
                <p class="text-neutral-600 dark:text-neutral-400">
                    Tổng quan hệ thống quản lý đoàn viên • <span
                        class="text-indigo-600 dark:text-indigo-400 font-medium">{{ now()->format('d/m/Y') }}</span>
                </p>
            </div>
        </div>

        <!-- Major Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Members Card -->
            <div class="premium-card stat-card p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">
                            Thành viên</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">{{ $totalMembers }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                ● {{ $activeMembers }} hoạt động
                            </span>
                        </div>
                    </div>
                    <div class="icon-gradient-blue group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Activities Card -->
            <div class="premium-card stat-card p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">
                            Hoạt động</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">{{ $totalActivities }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                🎯 {{ $upcomingActivities }} sắp tới
                            </span>
                        </div>
                    </div>
                    <div class="icon-gradient-purple group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrated Financial Statistics -->
        @livewire('admin.statistics', ['showHeader' => false])

        <!-- Activity Statistics section -->
        <div class="premium-card p-6">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-6 flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-full gradient-warning"></span>
                Thống kê hoạt động
            </h3>

            <div class="space-y-6">
                <!-- Top Row: Activity Distribution -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-center">
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">{{ $totalActivities }}
                        </p>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Tổng hoạt động</p>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-blue-50/30 dark:bg-blue-900/10 border-2 border-blue-500/50 text-center">
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                            {{ $upcomingActivities }}
                        </p>
                        <p class="text-sm font-medium text-blue-500 dark:text-blue-300">Sắp diễn ra</p>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-emerald-600 dark:bg-emerald-700 text-center shadow-lg shadow-emerald-200 dark:shadow-none">
                        <p class="text-3xl font-bold text-white mb-1">{{ $completedActivities }}</p>
                        <p class="text-sm font-medium text-emerald-50 text-center">Đã hoàn thành</p>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-center">
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">
                            {{ number_format($avgParticipation, 1) }}
                        </p>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">TB tham gia/hoạt động
                        </p>
                    </div>
                </div>

                <!-- Bottom Row: Registration Stats -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-3 gap-8 py-4 px-8 bg-neutral-50/50 dark:bg-neutral-800/30 rounded-2xl border border-dashed border-neutral-200 dark:border-neutral-700">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white mb-1">
                            {{ $totalRegistrations }}
                        </p>
                        <p
                            class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Tổng đăng ký</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                            {{ $approvedRegistrations }}
                        </p>
                        <p
                            class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Đã duyệt</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-amber-500 dark:text-amber-400 mb-1">
                            {{ $pendingRegistrations }}
                        </p>
                        <p
                            class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Chờ duyệt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>