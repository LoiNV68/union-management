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

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Activities Card -->
                <div class="premium-card stat-card p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Hoạt động</p>
                            <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">{{ $totalRegistrations }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    ✓ {{ $approvedRegistrations }} duyệt
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    ⏳ {{ $pendingRegistrations }} chờ
                                </span>
                            </div>
                        </div>
                        <div class="icon-gradient-purple group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Payments Card -->
                <div class="premium-card stat-card p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Khoản thu</p>
                            <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">{{ $totalTransactions }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    💰 {{ $paidTransactions }} đã trả
                                </span>
                            </div>
                        </div>
                        <div class="icon-gradient-green group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Training Points Average -->
                <div class="premium-card stat-card p-6 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Điểm TB</p>
                            <p class="text-3xl font-bold text-neutral-900 dark:text-white mt-1">
                                {{ number_format($avgPoints, 1) }}
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                    📚 {{ $trainingPoints->count() }} học kỳ
                                </span>
                            </div>
                        </div>
                        <div class="icon-gradient-orange group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Latest Training Point -->
                @if($latestPoint)
                    <div class="premium-card stat-card p-6 group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Điểm mới nhất</p>
                                <p class="text-3xl font-bold mt-1 {{ $latestPoint->point >= 90 ? 'text-green-600' : ($latestPoint->point >= 80 ? 'text-blue-600' : ($latestPoint->point >= 65 ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ number_format($latestPoint->point, 1) }}
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $latestPoint->point >= 90 ? 'score-excellent' : ($latestPoint->point >= 80 ? 'score-good' : ($latestPoint->point >= 65 ? 'score-average' : 'score-below')) }}">
                                        {{ $latestPoint->semester->school_year }} - HK{{ $latestPoint->semester->semester }}
                                    </span>
                                </div>
                            </div>
                            <div class="{{ $latestPoint->point >= 90 ? 'icon-gradient-green' : ($latestPoint->point >= 80 ? 'icon-gradient-blue' : ($latestPoint->point >= 65 ? 'icon-gradient-orange' : 'icon-gradient-pink')) }} group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="premium-card p-6 flex items-center justify-center">
                        <p class="text-sm text-neutral-500">Chưa có điểm rèn luyện</p>
                    </div>
                @endif
            </div>

            <!-- Charts Row -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Financial Summary -->
                <div class="premium-card p-6">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-full gradient-success"></span>
                        Tình hình tài chính
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Tổng khoản thu</span>
                            <span class="text-xl font-bold">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30">
                            <span class="font-medium text-green-700 dark:text-green-300">Đã thanh toán</span>
                            <span class="text-xl font-bold text-green-600">{{ number_format($paidAmount, 0, ',', '.') }}₫</span>
                        </div>
                        @if($totalAmount - $paidAmount > 0)
                            <div class="p-4 rounded-xl gradient-warning text-white">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium opacity-90">⚠️ Còn phải trả</span>
                                    <span class="text-2xl font-bold">{{ number_format($totalAmount - $paidAmount, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        @else
                            <div class="p-4 rounded-xl gradient-success text-white">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium opacity-90">✅ Đã hoàn thành</span>
                                    <span class="text-lg font-bold">Tất cả khoản thu</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Training Points Chart -->
                <div class="premium-card p-6">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-full gradient-orange"></span>
                        Điểm rèn luyện theo học kỳ
                    </h3>
                    @if($trainingPoints->count() > 0)
                        <div class="space-y-3">
                            @foreach($trainingPoints->take(5) as $tp)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-neutral-50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                        {{ $tp->semester->school_year }} - HK{{ $tp->semester->semester }}
                                    </span>
                                    <span class="text-lg font-bold px-3 py-1 rounded-lg
                                        {{ $tp->point >= 90 ? 'score-excellent' : '' }}
                                        {{ $tp->point >= 80 && $tp->point < 90 ? 'score-good' : '' }}
                                        {{ $tp->point >= 65 && $tp->point < 80 ? 'score-average' : '' }}
                                        {{ $tp->point < 65 ? 'score-below' : '' }}">
                                        {{ number_format($tp->point, 1) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <p class="text-neutral-500 font-medium">Chưa có điểm rèn luyện</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Registrations -->
            @if($recentRegistrations->count() > 0)
                <div class="premium-card p-6">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-full gradient-purple"></span>
                        Hoạt động đã đăng ký gần đây
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentRegistrations as $reg)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/50 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors border border-neutral-100 dark:border-neutral-700/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg {{ $reg->status == 1 ? 'gradient-success' : ($reg->status == 0 ? 'gradient-orange' : 'bg-red-500') }} flex items-center justify-center text-white">
                                        @if($reg->status == 1)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($reg->status == 0)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $reg->activity->activity_name }}</p>
                                        <p class="text-sm text-neutral-500">📅 {{ \Carbon\Carbon::parse($reg->activity->start_date)->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $reg->status == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $reg->status == 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $reg->status == 2 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                    {{ $reg->status == 1 ? '✓ Đã duyệt' : ($reg->status == 0 ? '⏳ Chờ duyệt' : '✗ Từ chối') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>