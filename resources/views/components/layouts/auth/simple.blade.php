<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        .auth-gradient-bg {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #667eea);
            background-size: 400% 400%;
            animation: gradient-animation 15s ease infinite;
        }

        @keyframes gradient-animation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .dark .auth-card {
            background: rgba(24, 24, 28, 0.95);
        }
    </style>
</head>

<body class="min-h-screen antialiased auth-gradient-bg">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-2">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                <div class="p-4 rounded-2xl bg-white/90 dark:bg-neutral-900/90 shadow-xl backdrop-blur-sm">
                    <img src="{{ asset('images/head-logo.svg') }}" class="h-16 w-auto" alt="{{ config('app.name') }}">
                </div>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="auth-card rounded-2xl shadow-2xl border border-white/20 dark:border-neutral-700/50 p-8 mt-4">
                {{ $slot }}
            </div>
            <p class="text-center text-sm text-white/80 mt-4">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
    @fluxScripts
</body>

</html>