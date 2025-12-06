<section>
    <!-- Header -->
    <div class="premium-card p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="icon-gradient-orange">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Điểm Rèn Luyện Của Bạn') }}
                </h1>
                <p class="text-neutral-600 dark:text-neutral-400 mt-1 flex items-center gap-2">
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                        🎓 {{ $member->user?->student_code }}
                    </span>
                    <span>{{ $member->full_name }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid gap-4 md:grid-cols-4 mb-6">
        <div class="md:col-span-2">
            <div class="premium-card p-4">
                <flux:select wire:model.live="filterSemesterId" label="📅 Lọc theo học kỳ">
                    <option value="">Tất cả học kỳ</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">
                            {{ $semester->school_year }} - Học kỳ {{ $semester->semester }}
                        </option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="premium-card stat-card p-4 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Tổng điểm</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white mt-1">
                        {{ number_format($totalPoints, 1) }}
                    </p>
                </div>
                <div class="icon-gradient-blue group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="premium-card stat-card p-4 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Điểm TB</p>
                    <p
                        class="text-2xl font-bold {{ $averagePoints >= 90 ? 'text-green-600' : ($averagePoints >= 80 ? 'text-blue-600' : ($averagePoints >= 65 ? 'text-yellow-600' : 'text-red-600')) }} mt-1">
                        {{ number_format($averagePoints, 1) }}
                    </p>
                </div>
                <div
                    class="{{ $averagePoints >= 90 ? 'icon-gradient-green' : ($averagePoints >= 80 ? 'icon-gradient-blue' : ($averagePoints >= 65 ? 'icon-gradient-orange' : 'icon-gradient-pink')) }} group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Points Cards -->
    <div class="space-y-4">
        @forelse ($trainingPoints as $tp)
            <div class="premium-card p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                                {{ $tp->semester->school_year }} - Học kỳ {{ $tp->semester->semester }}
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            {{ $tp->point >= 90 ? 'score-excellent' : '' }}
                                            {{ $tp->point >= 80 && $tp->point < 90 ? 'score-good' : '' }}
                                            {{ $tp->point >= 65 && $tp->point < 80 ? 'score-average' : '' }}
                                            {{ $tp->point < 65 ? 'score-below' : '' }}">
                                {{ $tp->point >= 90 ? '⭐ Xuất sắc' : ($tp->point >= 80 ? '👍 Tốt' : ($tp->point >= 65 ? '📚 Khá' : '📝 TB')) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-6 text-sm text-neutral-600 dark:text-neutral-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $tp->updater?->full_name ?? 'N/A' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $tp->updated_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        @if($tp->note)
                            <div class="mt-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/50">
                                <p class="text-sm text-neutral-700 dark:text-neutral-300">💬 {{ $tp->note }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="text-center ml-6">
                        <p class="text-sm text-neutral-500 mb-1">Điểm</p>
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center
                                        {{ $tp->point >= 90 ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                        {{ $tp->point >= 80 && $tp->point < 90 ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                        {{ $tp->point >= 65 && $tp->point < 80 ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                        {{ $tp->point < 65 ? 'bg-red-100 dark:bg-red-900/30' : '' }}">
                            <span class="text-3xl font-bold
                                            {{ $tp->point >= 90 ? 'text-green-600' : '' }}
                                            {{ $tp->point >= 80 && $tp->point < 90 ? 'text-blue-600' : '' }}
                                            {{ $tp->point >= 65 && $tp->point < 80 ? 'text-yellow-600' : '' }}
                                            {{ $tp->point < 65 ? 'text-red-600' : '' }}">
                                {{ number_format($tp->point, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-12 text-center">
                <div
                    class="w-20 h-20 mx-auto mb-4 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                    <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-lg font-semibold text-neutral-600 dark:text-neutral-400">{{ __('Chưa có điểm rèn luyện') }}
                </p>
                <p class="mt-2 text-sm text-neutral-500">
                    {{ __('Điểm rèn luyện của bạn sẽ được cập nhật bởi quản trị viên.') }}
                </p>
            </div>
        @endforelse
    </div>

    @if ($trainingPoints->count() > 0)
        <div class="mt-6 premium-card p-5 border-l-4 border-indigo-500">
            <div class="flex items-start gap-3">
                <div class="icon-gradient-purple flex-shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-neutral-900 dark:text-neutral-100 mb-2">Hướng dẫn phân loại</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <strong>Xuất sắc:</strong> 90 - 100</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <strong>Tốt:</strong> 80 - 89</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            <strong>Khá:</strong> 65 - 79</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <strong>Trung bình:</strong> &lt; 65</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>