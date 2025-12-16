<section x-data="{ showModal: @entangle('showModal') }" x-effect="showModal 
                    ? document.body.classList.add('overflow-hidden') 
                    : document.body.classList.remove('overflow-hidden')">

    <div class=" flex items-center justify-between">
        <flux:heading size="lg">{{ __('Quản lý thành viên') }}</flux:heading>
        <flux:button wire:click="openCreateForm" variant="primary">
            {{ __('Thêm Member') }}
        </flux:button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" label=""
                placeholder="Tìm kiếm theo mã SV, tên, email, số điện thoại, địa chỉ, chi đoàn..." type="text" />
        </div>
        <div class="w-40">
            <flux:select wire:model.live="perPage" label="">
                <option value="5">5 / trang</option>
                <option value="10">10 / trang</option>
                <option value="15">15 / trang</option>
                <option value="20">20 / trang</option>
                <option value="50">50 / trang</option>
            </flux:select>
        </div>
    </div>

    <!-- Unified Modal (Create/Edit/View) -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center p-4 "
            style="z-index: 9999; background-color: rgba(0, 0, 0, 0.5);" wire:click="closeModal">
            <div class="w-full max-w-2xl rounded-2xl  bg-white dark:bg-neutral-800 shadow-2xl relative" wire:click.stop="">
                <!-- Header -->
                <div
                    class="sticky top-0 bg-white dark:bg-neutral-800 border-b rounded-t-2xl
                                     border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                        @if ($modalMode === 'view')
                            {{ __('Thông tin Member') }}
                        @elseif ($modalMode === 'edit')
                            {{ __('Sửa thông tin Member') }}
                        @else
                            {{ __('Thêm Member mới') }}
                        @endif
                    </h2>
                    <button wire:click="closeModal"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 text-3xl font-bold leading-none transition-colors">
                        ×
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    <!-- Create/Edit Mode -->
                    <form wire:submit="saveMember" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input :disabled="$modalMode === 'view'" wire:model="full_name"
                                    :label="__('Họ và tên')" type="text" required wire:click.stop />
                            </div>
                            <div>
                                <flux:input :disabled="$modalMode === 'view'" wire:model="email" :label="__('Email')"
                                    type="email" required />
                            </div>
                            <div>
                                <x-date-picker :disabled="$modalMode === 'view'" wire:model="birth_date"
                                    :label="__('Ngày sinh')" required />
                                <flux:error name="birth_date" />
                            </div>
                            <div>
                                <flux:input :disabled="$modalMode === 'view'" wire:model="phone_number"
                                    :label="__('Số điện thoại')" type="text" />
                            </div>
                            <div>
                                <flux:select :disabled="$modalMode === 'view'" wire:model="gender" :label="__('Giới tính')"
                                    required>
                                    <option value="0">Nam</option>
                                    <option value="1">Nữ</option>
                                </flux:select>
                            </div>
                            <div>
                                <x-date-picker :disabled="$modalMode === 'view'" wire:model="join_date"
                                    :label="__('Ngày tham gia')" />
                                <flux:error name="join_date" />
                            </div>

                            <div>
                                <flux:select :disabled="$modalMode === 'view'" wire:model="status" :label="__('Trạng thái')"
                                    required>
                                    <option value="0">Không hoạt động</option>
                                    <option value="1">Hoạt động</option>
                                </flux:select>
                            </div>
                            <div>
                                <flux:select :disabled="$modalMode === 'view'" wire:model="branch_id"
                                    :label="__('Chi đoàn')">
                                    <option value="">-- Chọn chi đoàn --</option>
                                    @foreach ($this->branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="branch_id" />
                            </div>
                            <div>
                                <flux:select :disabled="$modalMode === 'view'" wire:model="user_id"
                                    :label="__('Tài khoản')">
                                    <option value="">-- Chọn tài khoản --</option>
                                    @foreach ($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->student_code }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="user_id" />
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea :disabled="$modalMode === 'view'" wire:model="address" :label="__('Địa chỉ')"
                                    rows="2" />
                            </div>
                        </div>
                    </form>

                </div>

                <!-- Footer -->
                <div
                    class="sticky bottom-0 rounded-b-2xl bg-neutral-50 dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-end gap-3">
                    @if ($modalMode === 'view')
                        <button wire:click="switchToEditMode"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            {{ __('Chỉnh sửa') }}
                        </button>
                        <button wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors">
                            {{ __('Đóng') }}
                        </button>
                    @else
                        <flux:button wire:click="closeModal" variant="ghost" type="button">
                            {{ __('Hủy') }}
                        </flux:button>
                        <flux:button wire:click="saveMember" variant="primary" type="button">
                            {{ $modalMode === 'edit' ? __('Cập nhật') : __('Thêm mới') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="w-full max-w-md rounded-lg bg-white dark:bg-neutral-800 p-6 shadow-xl" wire:click.stop>
                <flux:heading size="lg" class="mb-4">{{ __('Xác nhận xóa') }}</flux:heading>
                <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                    {{ __('Bạn có chắc chắn muốn xóa member này? Hành động này không thể hoàn tác.') }}
                </p>
                <div class="flex items-center justify-end gap-4">
                    <flux:button wire:click="closeDeleteModal" variant="ghost" type="button">
                        {{ __('Hủy') }}
                    </flux:button>
                    <flux:button wire:click="deleteMember" variant="danger" type="button">
                        {{ __('Xóa') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Members Table -->
    <div class="relative border border-neutral-200 dark:border-neutral-700 rounded-lg">
        <!-- Nội dung cuộn -->
        <div
            class="overflow-x-auto max-h-[500px] overflow-y-auto scrollbar-auto-hide hover:scrollbar-thin overscroll-contain">
            <table class="w-full border-separate border-spacing-0">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-800">
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-11 lg:left-0 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            STT
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-11 lg:left-14 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Mã SV
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-11 lg:sticky lg:left-[173px] lg:bg-white border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Họ và tên
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Ngày sinh
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Giới tính
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Email
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            SĐT
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Địa chỉ
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Chi đoàn
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Ngày vào Đoàn
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th
                            class="px-4 py-3 bg-white dark:bg-neutral-900 sticky top-0 z-10 lg:right-0 border-b border-neutral-200 dark:border-neutral-700 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($members as $index => $member)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            {{-- STT --}}
                            <td
                                class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100 lg:sticky lg:left-0 lg:bg-white dark:bg-neutral-900">
                                {{ $members->firstItem() + $index }}
                            </td>

                            {{-- MÃ SV — sticky left --}}
                            <td
                                class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100 whitespace-nowrap lg:sticky lg:left-14 lg:bg-white dark:bg-neutral-900  shadow-right">
                                {{ $member->user?->student_code ?? 'Chưa cập nhật' }}
                            </td>

                            {{-- HỌ VÀ TÊN — sticky left (đứng sau MÃ SV) --}}
                            <td
                                class="px-4 py-3 text-sm font-medium text-neutral-900 dark:text-neutral-100 whitespace-nowrap lg:sticky lg:left-[173px] lg:bg-white dark:bg-neutral-900  shadow-right">
                                {{ $member->full_name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                {{ $member->birth_date ? $member->birth_date->format('d/m/Y') : 'Chưa cập nhật' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $member->gender === 0 ? 'Nam' : 'Nữ' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $member->email ?? 'Chưa cập nhật' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                {{ $member->phone_number ?? 'Chưa cập nhật' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                <div class="max-w-xs truncate" title="{{ $member->address ?? 'Chưa cập nhật' }}">
                                    {{ $member->address ?? 'Chưa cập nhật' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                {{ $member->branch?->branch_name ?? 'Chưa cập nhật' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                {{ $member->join_date ? $member->join_date->format('d/m/Y') : 'Chưa cập nhật' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <flux:badge :variant="$member->status === 1 ? 'success' : 'neutral'">
                                    {{ $member->status === 1 ? 'Hoạt động' : 'Không hoạt động' }}
                                </flux:badge>
                            </td>

                            {{-- THAO TÁC — sticky right --}}
                            <td
                                class="px-4 py-3 text-sm lg:sticky lg:right-0 lg:bg-white dark:bg-neutral-900 shadow-left">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <flux:button wire:click="openViewModal({{ $member->id }})" variant="ghost" size="sm">
                                        {{ __('Xem') }}
                                    </flux:button>
                                    <flux:button wire:click="openEditForm({{ $member->id }})" variant="ghost" size="sm">
                                        {{ __('Sửa') }}
                                    </flux:button>
                                    <flux:button wire:click="openDeleteModal({{ $member->id }})" variant="danger" size="sm">
                                        {{ __('Xóa') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center text-neutral-400 dark:text-neutral-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="text-sm font-medium">{{ __('Không có dữ liệu') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>


    <!-- Pagination -->
    <div class="mt-4 space-y-2">
        {{ $members->onEachSide(1)->links() }}
    </div>

    <!-- Action Messages -->
    <x-action-message class="me-3" on="member-created">{{ __('Member đã được thêm thành công.') }}</x-action-message>
    <x-action-message class="me-3"
        on="member-updated">{{ __('Member đã được cập nhật thành công.') }}</x-action-message>
    <x-action-message class="me-3" on="member-deleted">{{ __('Member đã được xóa thành công.') }}</x-action-message>
</section>