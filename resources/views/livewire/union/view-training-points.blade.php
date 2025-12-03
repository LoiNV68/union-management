<section>
    <div class="mb-6">
        <flux:heading size="lg">{{ __('Điểm Rèn Luyện Của Bạn') }}</flux:heading>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
            {{ $member->full_name }} - {{ $member->user?->student_code }}
        </p>
    </div>

    <!-- Filter and Statistics -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <flux:select wire:model.live="filterSemesterId" label="Lọc theo học kỳ">
                <option value="">Tất cả học kỳ</option>
                @foreach ($semesters as $semester)
                    <option value="{{ $semester->id }}">
                        {{ $semester->school_year }} - Học kỳ {{ $semester->semester }}
                    </option>
                @endforeach
            </flux:select>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Tổng điểm</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($totalPoints, 2) }}
            </p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Điểm trung bình</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($averagePoints, 2) }}
            </p>
        </div>
    </div>

    <!-- Training Points Cards -->
    <div class="grid gap-4">
        @forelse ($trainingPoints as $tp)
            <div
                class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <flux:heading size="md">
                                {{ $tp->semester->school_year }} - Học kỳ {{ $tp->semester->semester }}
                            </flux:heading>
                            <flux:badge
                                :variant="$tp->point >= 90 ? 'success' : ($tp->point >= 80 ? 'primary' : ($tp->point >= 65 ? 'warning' : 'danger'))">
                                @if ($tp->point >= 90)
                                    Xuất sắc
                                @elseif($tp->point >= 80)
                                    Tốt
                                @elseif($tp->point >= 65)
                                    Khá
                                @else
                                    Trung bình
                                @endif
                            </flux:badge>
                        </div>

                        <div class="mt-3 space-y-1">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                <span class="font-semibold">Cập nhật bởi:</span>
                                {{ $tp->updater?->full_name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                <span class="font-semibold">Ngày cập nhật:</span>
                                {{ $tp->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Điểm</p>
                        <p class="text-4xl font-bold
                                    {{ $tp->point >= 90 ? 'text-green-600 dark:text-green-400' : '' }}
                                    {{ $tp->point >= 80 && $tp->point < 90 ? 'text-blue-600 dark:text-blue-400' : '' }}
                                    {{ $tp->point >= 65 && $tp->point < 80 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                    {{ $tp->point < 65 ? 'text-red-600 dark:text-red-400' : '' }}">
                            {{ number_format($tp->point, 2) }}
                        </p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">/ 100</p>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="rounded-lg border border-neutral-200 bg-neutral-50 p-12 text-center dark:border-neutral-700 dark:bg-neutral-900">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-4 text-lg font-semibold text-neutral-600 dark:text-neutral-400">
                    {{ __('Chưa có điểm rèn luyện') }}
                </p>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('Điểm rèn luyện của bạn sẽ được cập nhật bởi quản trị viên.') }}
                </p>
            </div>
        @endforelse
    </div>

    @if ($trainingPoints->count() > 0)
        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-900/20">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-400">
                        Hướng dẫn phân loại
                    </p>
                    <ul class="mt-2 space-y-1 text-sm text-blue-700 dark:text-blue-300">
                        <li>• <strong>Xuất sắc:</strong> 90 - 100 điểm</li>
                        <li>• <strong>Tốt:</strong> 80 - 89 điểm</li>
                        <li>• <strong>Khá:</strong> 65 - 79 điểm</li>
                        <li>• <strong>Trung bình:</strong> Dưới 65 điểm</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</section>