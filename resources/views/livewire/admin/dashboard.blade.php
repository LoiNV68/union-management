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

        <!-- Statistics Cards with Gradient Icons -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
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

            <!-- Finance Card -->
            <div class="premium-card stat-card p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">
                            Quỹ hiện tại</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">
                            {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}<span
                                class="text-lg">₫</span>
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                📊 {{ $paymentRate }}% thanh toán
                            </span>
                        </div>
                    </div>
                    <div class="icon-gradient-green group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Training Points Card -->
            <div class="premium-card stat-card p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">
                            Điểm TB</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">
                            {{ number_format($avgTrainingPoint, 1) }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                ⭐ {{ $totalTrainingPoints }} lượt chấm
                            </span>
                        </div>
                    </div>
                    <div class="icon-gradient-orange group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="premium-card p-6">
                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-full gradient-primary"></span>
                    Phân loại điểm rèn luyện
                </h3>
                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg score-excellent flex items-center justify-center text-sm font-bold">
                                A+</div>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Xuất sắc (≥90)</span>
                        </div>
                        <span
                            class="text-xl font-bold text-green-600 dark:text-green-400">{{ $trainingPointsDistribution['excellent'] }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/30">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg score-good flex items-center justify-center text-sm font-bold">
                                A</div>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Tốt (80-89)</span>
                        </div>
                        <span
                            class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $trainingPointsDistribution['good'] }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-900/30">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg score-average flex items-center justify-center text-sm font-bold">
                                B</div>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Khá (65-79)</span>
                        </div>
                        <span
                            class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $trainingPointsDistribution['average'] }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg score-below flex items-center justify-center text-sm font-bold">
                                C</div>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Trung bình (&lt;65)</span>
                        </div>
                        <span
                            class="text-xl font-bold text-red-600 dark:text-red-400">{{ $trainingPointsDistribution['below'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="premium-card p-6">
                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-full gradient-success"></span>
                    Tổng quan thu chi
                </h3>

                <div id="financial-chart" class="w-full min-h-[300px]"></div>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div
                        class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-center border border-red-100 dark:border-red-900/30">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Tổng chi</p>
                        <p class="text-lg font-bold text-red-600 dark:text-red-400">
                            {{ number_format($totalExpense, 0, ',', '.') }}₫
                        </p>
                    </div>
                    <div
                        class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 text-center border border-green-100 dark:border-green-900/30">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Qũy còn lại (Thực tế)</p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">
                            {{ number_format($actualRevenue - $totalExpense, 0, ',', '.') }}₫
                        </p>
                    </div>
                    <div
                        class="col-span-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-center border border-amber-100 dark:border-amber-900/30">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Dự kiến thu thêm</p>
                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">
                            {{ number_format($pendingRevenue, 0, ',', '.') }}₫
                        </p>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                document.addEventListener('livewire:init', () => {
                    const options = {
                        series: [{{ $totalExpense }}, {{ max(0, $actualRevenue - $totalExpense) }}, {{ $pendingRevenue }}],
                        chart: {
                            type: 'donut',
                            height: 380,
                            fontFamily: 'inherit',
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                            }
                        },
                        labels: ['Tổng chi', 'Quỹ đã thu', 'Chưa thu'],
                        colors: ['#ef4444', '#10b981', '#f59e0b'],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '14px',
                                            fontFamily: 'inherit',
                                            offsetY: -10
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '24px',
                                            fontFamily: 'inherit',
                                            fontWeight: 600,
                                            offsetY: 16,
                                            formatter: function (val) {
                                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Thực thu',
                                            fontSize: '14px',
                                            fontFamily: 'inherit',
                                            fontWeight: 500,
                                            color: '#6b7280',
                                            formatter: function (w) {
                                                // Display Actual Revenue in center
                                                const actual = {{ $actualRevenue }};
                                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(actual);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: false,
                        },
                        legend: {
                            position: 'bottom',
                            fontFamily: 'inherit',
                            markers: {
                                radius: 12
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function (val) {
                                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
                                }
                            }
                        }
                    };

                    const chart = new ApexCharts(document.querySelector("#financial-chart"), options);
                    chart.render();

                    // Re-render on theme change if needed (optional)
                    // ... 
                });
            </script>
        </div>

        <!-- Activity Statistics -->
        <div class="premium-card p-6">
            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-6 flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-full gradient-warning"></span>
                Thống kê hoạt động
            </h3>
            
            <div class="space-y-6">
                <!-- Top Row: Activity Distribution -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-center">
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">{{ $totalActivities }}</p>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Tổng hoạt động</p>
                    </div>
                    
                    <div class="p-5 rounded-2xl bg-blue-50/30 dark:bg-blue-900/10 border-2 border-blue-500/50 text-center">
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ $upcomingActivities }}</p>
                        <p class="text-sm font-medium text-blue-500 dark:text-blue-300">Sắp diễn ra</p>
                    </div>
                    
                    <div class="p-5 rounded-2xl bg-emerald-600 dark:bg-emerald-700 text-center shadow-lg shadow-emerald-200 dark:shadow-none">
                        <p class="text-3xl font-bold text-white mb-1">{{ $completedActivities }}</p>
                        <p class="text-sm font-medium text-emerald-50 text-center">Đã hoàn thành</p>
                    </div>
                    
                    <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 text-center">
                        <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">{{ number_format($avgParticipation, 1) }}</p>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">TB tham gia/hoạt động</p>
                    </div>
                </div>

                <!-- Bottom Row: Registration Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 py-4 px-8 bg-neutral-50/50 dark:bg-neutral-800/30 rounded-2xl border border-dashed border-neutral-200 dark:border-neutral-700">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white mb-1">{{ $totalRegistrations }}</p>
                        <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Tổng đăng ký</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1">{{ $approvedRegistrations }}</p>
                        <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Đã duyệt</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-amber-500 dark:text-amber-400 mb-1">{{ $pendingRegistrations }}</p>
                        <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Chờ duyệt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>