<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-neutral-50 dark:bg-neutral-900">
    <flux:sidebar sticky stashable
        class="border-e z-100! border-neutral-200/80 bg-white dark:border-neutral-800 dark:bg-neutral-950 overflow-hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Logo Section -->
        <a href="{{ route('dashboard') }}"
            class="group flex items-center gap-3 px-2 py-3 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors"
            wire:navigate>
            <div
                class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                <img src="{{ asset('images/head-logo.svg') }}" alt="logo" class="w-10 h-10 rounded-sm">
            </div>
            <span class="text-lg font-bold text-neutral-900 dark:text-white">Union MS</span>
        </a>

        <div class="mt-4">
            <flux:navlist variant="outline">
                {{-- Menu chung cho tất cả --}}
                <flux:navlist.group class="grid">
                    <p
                        class="px-3 py-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('TRANG CHỦ') }}
                    </p>
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        wire:navigate
                        class="rounded-lg mx-1 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 {{ request()->routeIs('dashboard') ? '!bg-indigo-100 dark:!bg-indigo-900/30 !text-indigo-700 dark:!text-indigo-300' : '' }}">
                        {{ __('Trang chủ') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                {{-- Menu dành cho Admin (role 2 - Super Admin) --}}
                @if(in_array(auth()->user()?->role, [2]))
                    <flux:navlist.group class="grid mt-4">
                        <p
                            class="px-3 py-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                            {{ __('QUẢN TRỊ') }}
                        </p>
                        <flux:navlist.item icon="lock-closed" :href="route('admin.permission')"
                            :current="request()->routeIs('admin.permission')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ request()->routeIs('admin.permission') ? '!bg-purple-100 dark:!bg-purple-900/30 !text-purple-700 dark:!text-purple-300' : '' }}">
                            {{ __('Quyền truy cập') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="users" :href="route('admin.members')"
                            :current="request()->routeIs('admin.members')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.members') ? '!bg-blue-100 dark:!bg-blue-900/30 !text-blue-700 dark:!text-blue-300' : '' }}">
                            {{ __('Thành viên') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="building-library" :href="route('admin.branches')"
                            :current="request()->routeIs('admin.branches')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-teal-50 dark:hover:bg-teal-900/20 {{ request()->routeIs('admin.branches') ? '!bg-teal-100 dark:!bg-teal-900/30 !text-teal-700 dark:!text-teal-300' : '' }}">
                            {{ __('Chi đoàn') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="calendar-days" :href="route('admin.activities')"
                            :current="request()->routeIs('admin.activities')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-orange-50 dark:hover:bg-orange-900/20 {{ request()->routeIs('admin.activities') ? '!bg-orange-100 dark:!bg-orange-900/30 !text-orange-700 dark:!text-orange-300' : '' }}">
                            {{ __('Hoạt động') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="wallet" :href="route('admin.transactions')"
                            :current="request()->routeIs('admin.transactions')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-green-50 dark:hover:bg-green-900/20 {{ request()->routeIs('admin.transactions') ? '!bg-green-100 dark:!bg-green-900/30 !text-green-700 dark:!text-green-300' : '' }}">
                            {{ __('Thu chi') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('admin.training-points')"
                            :current="request()->routeIs('admin.training-points')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-amber-50 dark:hover:bg-amber-900/20 {{ request()->routeIs('admin.training-points') ? '!bg-amber-100 dark:!bg-amber-900/30 !text-amber-700 dark:!text-amber-300' : '' }}">
                            {{ __('Điểm rèn luyện') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="calendar" :href="route('admin.semesters')"
                            :current="request()->routeIs('admin.semesters')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-pink-50 dark:hover:bg-pink-900/20 {{ request()->routeIs('admin.semesters') ? '!bg-pink-100 dark:!bg-pink-900/30 !text-pink-700 dark:!text-pink-300' : '' }}">
                            {{ __('Học kỳ') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                {{-- Menu dành cho Admin (role 1 - Branch Admin) --}}
                @if(in_array(auth()->user()?->role, [1]))
                    <flux:navlist.group class="grid mt-4">
                        <p
                            class="px-3 py-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ __('QUẢN TRỊ') }}
                        </p>
                        <flux:navlist.item icon="users" :href="route('admin.members')"
                            :current="request()->routeIs('admin.members')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.members') ? '!bg-blue-100 dark:!bg-blue-900/30 !text-blue-700 dark:!text-blue-300' : '' }}">
                            {{ __('Thành viên') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="calendar-days" :href="route('admin.activities')"
                            :current="request()->routeIs('admin.activities')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-orange-50 dark:hover:bg-orange-900/20 {{ request()->routeIs('admin.activities') ? '!bg-orange-100 dark:!bg-orange-900/30 !text-orange-700 dark:!text-orange-300' : '' }}">
                            {{ __('Hoạt động') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="wallet" :href="route('admin.transactions')"
                            :current="request()->routeIs('admin.transactions')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-green-50 dark:hover:bg-green-900/20 {{ request()->routeIs('admin.transactions') ? '!bg-green-100 dark:!bg-green-900/30 !text-green-700 dark:!text-green-300' : '' }}">
                            {{ __('Thu chi') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('admin.training-points')"
                            :current="request()->routeIs('admin.training-points')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-amber-50 dark:hover:bg-amber-900/20 {{ request()->routeIs('admin.training-points') ? '!bg-amber-100 dark:!bg-amber-900/30 !text-amber-700 dark:!text-amber-300' : '' }}">
                            {{ __('Điểm rèn luyện') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                {{-- Menu dành cho Member (role 0) --}}
                @if(auth()->user()?->role === 0)
                    <flux:navlist.group class="grid mt-4">
                        <p
                            class="px-3 py-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            {{ __('THÀNH VIÊN') }}
                        </p>
                        <flux:navlist.item icon="calendar-days" :href="route('union.activities')"
                            :current="request()->routeIs('union.activities')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ request()->routeIs('union.activities') ? '!bg-purple-100 dark:!bg-purple-900/30 !text-purple-700 dark:!text-purple-300' : '' }}">
                            {{ __('Hoạt động') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="wallet" :href="route('union.transactions')"
                            :current="request()->routeIs('union.transactions')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-green-50 dark:hover:bg-green-900/20 {{ request()->routeIs('union.transactions') ? '!bg-green-100 dark:!bg-green-900/30 !text-green-700 dark:!text-green-300' : '' }}">
                            {{ __('Khoản thu') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('union.training-points')"
                            :current="request()->routeIs('union.training-points')" wire:navigate
                            class="rounded-lg mx-1 hover:bg-orange-50 dark:hover:bg-orange-900/20 {{ request()->routeIs('union.training-points') ? '!bg-orange-100 dark:!bg-orange-900/30 !text-orange-700 dark:!text-orange-300' : '' }}">
                            {{ __('Điểm rèn luyện') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>
        </div>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <div class="p-2">
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile :name="auth()->user()->full_name" :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                    class="rounded-xl bg-neutral-100 dark:bg-neutral-800/50 hover:bg-neutral-200 dark:hover:bg-neutral-800" />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-2 text-sm font-normal">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white font-bold text-sm">
                                    {{ auth()->user()->initials() }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-neutral-900 dark:text-white truncate">
                                        {{ auth()->user()->full_name }}
                                    </p>
                                    <p class="text-xs text-neutral-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Cài đặt') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full text-red-600 dark:text-red-400" data-test="logout-button">
                            {{ __('Đăng xuất') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden bg-white dark:bg-neutral-950 border-b border-neutral-200 dark:border-neutral-800">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-2 text-sm font-normal">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white font-bold text-sm">
                                {{ auth()->user()->initials() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-neutral-900 dark:text-white truncate">
                                    {{ auth()->user()->full_name }}
                                </p>
                                <p class="text-xs text-neutral-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Cài đặt') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full text-red-600 dark:text-red-400" data-test="logout-button">
                        {{ __('Đăng xuất') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>