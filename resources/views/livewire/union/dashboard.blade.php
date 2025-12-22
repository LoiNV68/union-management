<x-layouts.app :title="__('Bảng Điều Khiển')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @if(isset($error))
            <div class="premium-card p-6 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-900">
                <p class="text-red-600 dark:text-red-400">{{ $error }}</p>
            </div>
        @else
            <!-- Header with Welcome Message -->
            <div class="premium-card p-6 relative overflow-hidden">
                <div class="absolute inset-0 gradient-info opacity-5"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl gradient-primary flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ substr($member->full_name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            Xin chào, {{ $member->full_name }}! 👋
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400 flex items-center gap-3 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                🎓 {{ $member->user?->student_code }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                🏛️ {{ $member->branch?->branch_name ?? 'Chưa phân chi đoàn' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="premium-card p-5 group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl gradient-purple flex items-center justify-center text-white shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Tổng Đăng Ký</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $totalRegistrations }}</p>
                    </div>
                </div>

                <div class="premium-card p-5 group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl gradient-success flex items-center justify-center text-white shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Đã Duyệt</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $approvedRegistrations }}</p>
                    </div>
                </div>

                <div class="premium-card p-5 group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl gradient-info flex items-center justify-center text-white shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Đã Thanh Toán</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $paidTransactions }}</p>
                    </div>
                </div>

                <div class="premium-card p-5 group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl gradient-warning flex items-center justify-center text-white shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Chưa Đóng</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $pendingPayments }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:grid-cols-12">
                <!-- Left Column: Financial Status (40%) -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <div class="premium-card p-6 h-full">
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 rounded-full gradient-success"></span>
                            Tình Hình Tài Chính
                        </h3>
                        
                        <script src="https://cdn.jsdelivr.net/npm/apexcharts" data-navigate-once></script>

                        <div 
                            id="union-financial-chart" 
                            class="w-full min-h-[300px] mb-6"
                            x-init="
                                const options = {
                                    series: [{{ $paidAmount }}, {{ max(0, $totalAmount - $paidAmount) }}],
                                    chart: {
                                        type: 'donut',
                                        height: 320,
                                        fontFamily: 'inherit',
                                    },
                                    labels: ['Đã đóng', 'Chưa đóng'],
                                    colors: ['#10b981', '#f59e0b'],
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                size: '75%',
                                                labels: {
                                                    show: true,
                                                    name: { show: true, fontSize: '14px', offsetY: -10 },
                                                    value: {
                                                        show: true,
                                                        fontSize: '24px',
                                                        fontWeight: 700,
                                                        offsetY: 10,
                                                        formatter: (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
                                                    },
                                                    total: {
                                                        show: true,
                                                        label: 'Tổng thu',
                                                        formatter: () => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format({{ $totalAmount }})
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    dataLabels: { enabled: false },
                                    legend: { position: 'bottom' },
                                    tooltip: {
                                        y: {
                                            formatter: (val) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
                                        }
                                    }
                                };
                                new ApexCharts($el, options).render();
                            "
                        ></div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100/50 dark:border-emerald-900/20 transition-all hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Đã hoàn thành</span>
                                </div>
                                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($paidAmount, 0, ',', '.') }}₫</span>
                            </div>
                            
                            @if($totalAmount - $paidAmount > 0)
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100/50 dark:border-amber-900/20 transition-all hover:bg-amber-50 dark:hover:bg-amber-900/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                        <span class="text-sm font-semibold text-amber-800 dark:text-amber-300 uppercase tracking-wider">Còn nợ</span>
                                    </div>
                                    <span class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ number_format($totalAmount - $paidAmount, 0, ',', '.') }}₫</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Activity Insights (60%) -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    <!-- Activity Stats Grid -->
                    <div class="premium-card p-6">
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 rounded-full gradient-warning"></span>
                            Hoạt Động Cá Nhân
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-5 rounded-2xl bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30">
                                <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Sắp diễn ra</p>
                                <p class="text-3xl font-black text-blue-700 dark:text-blue-400">{{ $upcomingRegistrations }}</p>
                            </div>
                            
                            <div class="p-5 rounded-2xl bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30">
                                <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Đã tham gia</p>
                                <p class="text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ $completedRegistrations }}</p>
                            </div>

                            <div class="p-5 rounded-2xl bg-amber-50/30 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                                <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1">Đang chờ duyệt</p>
                                <p class="text-3xl font-black text-amber-700 dark:text-amber-400">{{ $pendingRegistrations }}</p>
                            </div>

                            <div class="p-5 rounded-2xl bg-purple-50/30 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-900/30">
                                <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-1">Tỷ lệ duyệt</p>
                                @php $rate = $totalRegistrations > 0 ? ($approvedRegistrations / $totalRegistrations) * 100 : 0; @endphp
                                <p class="text-3xl font-black text-purple-700 dark:text-purple-400">{{ number_format($rate, 0) }}%</p>
                            </div>
                        </div>

                        <!-- Progress Bar or Extra Info -->
                        <div class="mt-6 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Hành trình hoạt động</span>
                                <span class="text-xs font-bold text-neutral-900 dark:text-white">{{ $completedRegistrations }} / {{ $totalRegistrations }}</span>
                            </div>
                            <div class="w-full h-2 bg-neutral-200 dark:bg-neutral-700 rounded-full overflow-hidden">
                                <div class="h-full gradient-primary transition-all duration-1000" style="width: {{ $totalRegistrations > 0 ? ($completedRegistrations / $totalRegistrations) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities (More Compact) -->
                    @if($recentRegistrations->count() > 0)
                        <div class="premium-card p-6">
                            <h3 class="text-sm font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-4">Các hoạt động đăng ký gần nhất</h3>
                            <div class="space-y-3">
                                @foreach($recentRegistrations as $reg)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50/50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-700/50 group hover:border-blue-300 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2.5 h-2.5 rounded-full {{ $reg->registration_status == 1 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($reg->registration_status == 0 ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                                            <span class="font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-blue-600 transition-colors">{{ $reg->activity->activity_name }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-neutral-200 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-400">
                                                {{ \Carbon\Carbon::parse($reg->activity->start_date)->format('d/m') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>