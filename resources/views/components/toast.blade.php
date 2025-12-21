@props(['duration' => 3000])

<div x-data="{
        toasts: [],
        add(event) {
            const id = Date.now();
            const toast = {
                id: id,
                text: event.detail.text,
                variant: event.detail.variant || 'success',
                heading: event.detail.heading,
                visible: true
            };
            this.toasts.push(toast);
            setTimeout(() => {
                 this.remove(id);
            }, {{ $duration }});
        },
        remove(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300); // Wait for transition
            }
        }
    }" @toast.window="add($event)" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" x-transition:enter="transition transform ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition transform ease-in duration-300"
            x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
            class="pointer-events-auto min-w-[300px] max-w-sm w-full bg-white dark:bg-neutral-800 shadow-lg rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="p-4 flex items-start gap-3">
                <!-- Icons based on variant -->
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.variant === 'success'">
                        <div
                            class="rounded-full bg-green-100 dark:bg-green-900/30 p-1 text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="toast.variant === 'danger' || toast.variant === 'error'">
                        <div class="rounded-full bg-red-100 dark:bg-red-900/30 p-1 text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="toast.variant === 'info'">
                        <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-1 text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="toast.variant === 'warning'">
                        <div
                            class="rounded-full bg-yellow-100 dark:bg-yellow-900/30 p-1 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </template>
                </div>

                <div class="flex-1 pt-0.5">
                    <p x-text="toast.heading" class="text-sm font-semibold text-neutral-900 dark:text-white mb-1"></p>
                    <p x-text="toast.text" class="text-sm text-neutral-600 dark:text-neutral-400"></p>
                </div>

                <button @click="remove(toast.id)"
                    class="text-neutral-400 hover:text-neutral-500 dark:hover:text-neutral-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>