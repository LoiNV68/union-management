<section>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @if($showHeader)
            <!-- Header -->
            <div class="premium-card p-6 relative overflow-hidden">
                <div class="absolute inset-0 gradient-primary opacity-5"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center gap-3">
                            <div class="icon-gradient-indigo">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            Thống kê hệ thống
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400">
                            Thống kê chi tiết về thu chi và hoạt động của hệ thống
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="premium-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">Bộ lọc</h3>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <flux:field>
                        <flux:label>Khoảng thời gian</flux:label>
                        <flux:select wire:model.live="period">
                            <option value="all">Tất cả</option>
                            <option value="month">Tháng này</option>
                            <option value="year">Năm nay</option>
                            <option value="custom">Tùy chọn</option>
                        </flux:select>
                    </flux:field>
                </div>

                @if($period === 'custom')
                    <div>
                        <flux:field>
                            <flux:label>Từ ngày</flux:label>
                            <flux:input type="date" wire:model.live="startDate" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Đến ngày</flux:label>
                            <flux:input type="date" wire:model.live="endDate" />
                        </flux:field>
                    </div>
                @endif

                @if(auth()->user()->role === 2)
                    <div>
                        <flux:field>
                            <flux:label>Chi đoàn</flux:label>
                            <flux:select wire:model.live="selectedBranchId">
                                <option value="">Tất cả chi đoàn</option>
                                @foreach($this->branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                @endif

                <div class="flex items-end">
                    <flux:button wire:click="resetFilters" variant="ghost" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Đặt lại
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Revenue Card -->
            <div
                class="premium-card stat-card p-6 group relative overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-green-200 dark:bg-green-900/20 rounded-full -mr-16 -mt-16 opacity-20">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="icon-gradient-green group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2 uppercase tracking-wide">
                        Tổng thu (đã thu)
                    </h3>
                    <p class="text-3xl font-bold text-green-600 mb-2">
                        {{ number_format($this->summaryStatistics['actual_revenue'], 0, ',', '.') }}<span
                            class="text-lg">₫</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-neutral-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span>Tổng: {{ number_format($this->summaryStatistics['total_revenue'], 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>

            <!-- Expense Card -->
            <div
                class="premium-card stat-card p-6 group relative overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-red-200 dark:bg-red-900/20 rounded-full -mr-16 -mt-16 opacity-20">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="icon-gradient-red group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2 uppercase tracking-wide">
                        Tổng chi
                    </h3>
                    <p class="text-3xl font-bold text-red-600 mb-2">
                        {{ number_format($this->summaryStatistics['total_expense'], 0, ',', '.') }}<span
                            class="text-lg">₫</span>
                    </p>
                </div>
            </div>

            <!-- Net Profit Card -->
            <div
                class="premium-card stat-card p-6 group relative overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-blue-200 dark:bg-blue-900/20 rounded-full -mr-16 -mt-16 opacity-20">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="icon-gradient-blue group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2 uppercase tracking-wide">
                        Lợi nhuận ròng
                    </h3>
                    <p
                        class="text-3xl font-bold {{ $this->summaryStatistics['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                        {{ number_format($this->summaryStatistics['net_profit'], 0, ',', '.') }}<span
                            class="text-lg">₫</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs">
                        <span
                            class="px-2 py-1 rounded-full {{ $this->summaryStatistics['net_profit'] >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ $this->summaryStatistics['net_profit'] >= 0 ? 'Lãi' : 'Lỗ' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Rate Card -->
            <div
                class="premium-card stat-card p-6 group relative overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-purple-200 dark:bg-purple-900/20 rounded-full -mr-16 -mt-16 opacity-20">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="icon-gradient-purple group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2 uppercase tracking-wide">
                        Tỷ lệ thanh toán
                    </h3>
                    <p class="text-3xl font-bold text-purple-600 mb-2">
                        {{ number_format($this->summaryStatistics['payment_rate'], 2) }}<span class="text-lg">%</span>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-neutral-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ $this->summaryStatistics['paid_transactions'] }}/{{ $this->summaryStatistics['total_member_transactions'] }}
                            đã thanh toán</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Expense Pie Chart -->
        <div class="premium-card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="icon-gradient-indigo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        Phân bổ thu chi
                    </h2>
                </div>
            </div>
            <div id="monthly-chart" style="height: 400px;"></div>
        </div>

        <!-- Branch Statistics -->
        <div class="premium-card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="icon-gradient-teal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        Thống kê theo chi đoàn
                    </h2>
                </div>
            </div>
            <div class="overflow-x-auto -mx-6 px-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-neutral-200 dark:border-neutral-700">
                            <th class="text-left py-4 px-4 font-semibold text-neutral-900 dark:text-neutral-100">Chi
                                đoàn</th>
                            <th class="text-right py-4 px-4 font-semibold text-neutral-900 dark:text-neutral-100">Thành
                                viên</th>
                            <th class="text-right py-4 px-4 font-semibold text-neutral-900 dark:text-neutral-100">Tổng
                                thu</th>
                            <th class="text-right py-4 px-4 font-semibold text-neutral-900 dark:text-neutral-100">Thanh
                                toán</th>
                            <th class="text-right py-4 px-4 font-semibold text-neutral-900 dark:text-neutral-100">Tỷ lệ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->branchStatistics as $stat)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors duration-150">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-bold">
                                            {{ substr($stat['branch']->branch_name, 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-neutral-900 dark:text-neutral-100">
                                            {{ $stat['branch']->branch_name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="inline-flex items-center gap-1 text-neutral-700 dark:text-neutral-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ $stat['total_members'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="font-semibold text-green-600 dark:text-green-400">
                                        {{ number_format($stat['revenue'], 0, ',', '.') }} ₫
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="text-neutral-600 dark:text-neutral-400">
                                        <span class="font-semibold text-green-600">{{ $stat['paid_transactions'] }}</span>
                                        <span class="text-neutral-400">/</span>
                                        <span>{{ $stat['transactions'] }}</span>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div
                                            class="w-20 bg-neutral-200 dark:bg-neutral-700 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $stat['payment_rate'] >= 80 ? 'bg-gradient-to-r from-green-400 to-green-600' : ($stat['payment_rate'] >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-red-400 to-red-600') }}"
                                                style="width: {{ min($stat['payment_rate'], 100) }}%"></div>
                                        </div>
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold min-w-[60px] text-center {{ $stat['payment_rate'] >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($stat['payment_rate'] >= 50 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                            {{ number_format($stat['payment_rate'], 1) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-16 h-16 text-neutral-300 dark:text-neutral-700" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-neutral-500 dark:text-neutral-400">Không có dữ liệu</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        function renderPieChart() {
            const summaryData = @json($this->summaryStatistics);

            // Check if we have valid data
            if (!summaryData || (summaryData.actual_revenue === 0 && summaryData.total_expense === 0)) {
                const chartElement = document.getElementById('monthly-chart');
                if (chartElement) {
                    chartElement.innerHTML = '<div class="flex items-center justify-center h-full text-neutral-500 dark:text-neutral-400"><p>Chưa có dữ liệu để hiển thị</p></div>';
                }
                return;
            }

            // Prepare data for pie chart - ensure we have numbers
            const revenue = parseFloat(summaryData.actual_revenue) || 0;
            const expense = parseFloat(summaryData.total_expense) || 0;

            const options = {
                series: [revenue, expense],
                chart: {
                    type: 'pie',
                    height: 400,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                labels: ['Thu', 'Chi'],
                colors: ['#10b981', '#ef4444'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        const label = opts.w.globals.labels[opts.seriesIndex];
                        const value = opts.w.globals.series[opts.seriesIndex];
                        const percentage = typeof val === 'number' ? val : parseFloat(val) || 0;
                        return label + ': ' + new Intl.NumberFormat('vi-VN').format(value || 0) + ' ₫\n' + percentage.toFixed(1) + '%';
                    },
                    style: {
                        fontSize: '13px',
                        fontWeight: 600,
                        colors: ['#ffffff']
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 1,
                        left: 1,
                        blur: 1,
                        opacity: 0.45
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontWeight: 500,
                    fontFamily: 'inherit',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 6
                    },
                    formatter: function (seriesName, opts) {
                        const value = opts.w.globals.series[opts.seriesIndex] || 0;
                        const percentageValue = opts.w.globals.seriesPercent[opts.seriesIndex];
                        const percentage = typeof percentageValue === 'number' ? percentageValue : parseFloat(percentageValue) || 0;
                        return seriesName + ': ' + new Intl.NumberFormat('vi-VN').format(value) + ' ₫ (' + percentage.toFixed(1) + '%)';
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val, opts) {
                            const percentageValue = opts.w.globals.seriesPercent ? opts.w.globals.seriesPercent[opts.seriesIndex] : null;
                            const percentage = typeof percentageValue === 'number' ? percentageValue : (percentageValue ? parseFloat(percentageValue) : 0) || 0;
                            return new Intl.NumberFormat('vi-VN').format(val || 0) + ' VNĐ (' + percentage.toFixed(1) + '%)';
                        }
                    },
                    style: {
                        fontSize: '13px'
                    }
                },
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        donut: {
                            size: '0%'
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chartElement = document.getElementById('monthly-chart');
            if (!chartElement) {
                return;
            }

            // Destroy existing chart if it exists
            if (chartElement._apexChart) {
                chartElement._apexChart.destroy();
                chartElement._apexChart = null;
            }

            // Create new chart
            chartElement._apexChart = new ApexCharts(chartElement, options);
            chartElement._apexChart.render();
        }

        // Initialize on Livewire init
        document.addEventListener('livewire:init', () => {
            renderPieChart();
        });

        // Re-render when Livewire updates
        document.addEventListener('livewire:update', () => {
            setTimeout(() => {
                renderPieChart();
            }, 300);
        });

        // Also render on initial page load (fallback)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    renderPieChart();
                }, 500);
            });
        } else {
            setTimeout(() => {
                renderPieChart();
            }, 500);
        }
    </script>
</section>