<section>
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý Điểm Rèn Luyện') }}</flux:heading>
        <flux:button wire:click="openCreateForm" variant="primary">
            {{ __('Thêm Điểm Rèn Luyện') }}
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label="" placeholder="Tìm kiếm theo tên hoặc mã SV..."
                type="text" />
        </div>
        <div class="w-48">
            <flux:select wire:model.live="filterSemesterId" label="">
                <option value="">Tất cả học kỳ</option>
                @foreach ($semesters as $semester)
                    <option value="{{ $semester->id }}">
                        {{ $semester->school_year }} - HK{{ $semester->semester }}
                    </option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-48">
            <flux:select wire:model.live="filterBranchId" label="">
                <option value="">Tất cả chi hội</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-40">
            <flux:select wire:model.live="perPage" label="">
                <option value="5">5 / trang</option>
                <option value="10">10 / trang</option>
                <option value="20">20 / trang</option>
                <option value="50">50 / trang</option>
            </flux:select>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateForm">
            <div class="w-full max-w-xl rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $editingId ? __('Sửa Điểm Rèn Luyện') : __('Thêm Điểm Rèn Luyện Mới') }}
                    </flux:heading>
                    <flux:button wire:click="closeCreateForm" variant="ghost" size="sm">×</flux:button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <flux:select wire:model="member_id" label="Thành viên" required
                            :disabled="$editingId ? true : false">
                            <option value="">Chọn thành viên</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->full_name }} - {{ $member->user?->student_code }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:select wire:model="semester_id" label="Học kỳ" required :disabled="$editingId ? true : false">
                            <option value="">Chọn học kỳ</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}">
                                    {{ $semester->school_year }} - Học kỳ {{ $semester->semester }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:input wire:model="point" label="Điểm" type="number" step="0.01" min="0" max="100" required
                            placeholder="0.00" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <flux:button wire:click="closeCreateForm" variant="ghost" type="button">
                            {{ __('Hủy') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ $editingId ? __('Cập nhật') : __('Thêm mới') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <flux:heading size="lg" class="mb-4">{{ __('Xác nhận xóa') }}</flux:heading>
                <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                    {{ __('Bạn có chắc chắn muốn xóa điểm rèn luyện này?') }}
                </p>
                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Hủy') }}</flux:button>
                    <flux:button wire:click="delete" variant="danger">{{ __('Xóa') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Training Points List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        STT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Mã SV
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Họ và tên
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Chi hội
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Học kỳ
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Điểm
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Cập nhật bởi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Ngày cập nhật
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
                @forelse ($trainingPoints as $index => $tp)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $trainingPoints->firstItem() + $index }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $tp->member->user?->student_code ?? 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $tp->member->full_name }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $tp->member->branch?->branch_name ?? 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $tp->semester->school_year }} - HK{{ $tp->semester->semester }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5
                                                        {{ $tp->point >= 90 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                                        {{ $tp->point >= 80 && $tp->point < 90 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                                        {{ $tp->point >= 65 && $tp->point < 80 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                                        {{ $tp->point < 65 ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                {{ number_format($tp->point, 2) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $tp->updater?->full_name ?? 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $tp->updated_at->format('d/m/Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEditForm({{ $tp->id }})" variant="ghost" size="sm">
                                    {{ __('Sửa') }}
                                </flux:button>
                                <flux:button wire:click="openDeleteModal({{ $tp->id }})" variant="danger" size="sm">
                                    {{ __('Xóa') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-neutral-500">
                            {{ __('Chưa có điểm rèn luyện nào.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $trainingPoints->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="training-point-created">{{ __('Đã thêm điểm rèn luyện.') }}
    </x-action-message>
    <x-action-message class="me-3" on="training-point-updated">{{ __('Đã cập nhật điểm rèn luyện.') }}
    </x-action-message>
    <x-action-message class="me-3" on="training-point-deleted">{{ __('Đã xóa điểm rèn luyện.') }}
    </x-action-message>
</section>