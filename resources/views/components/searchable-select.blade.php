@props([
    'label' => null,
    'placeholder' => 'Chọn một tùy chọn...',
    'searchPlaceholder' => 'Tìm kiếm...',
    'items' => [], // Expecting an array of ['value' => ..., 'label' => ...]
    'value' => null,
    'size' => null,
])

@php
    $wireModel = $attributes->wire('model');
    $wireModelName = $wireModel ? $wireModel->value() : null;
@endphp

<div x-data="{
    open: false,
    search: '',
    items: {{ json_encode($items) }},
    value: @if($wireModelName) @entangle($wireModelName) @else {{ json_encode($value) }} @endif,
    dropdownStyle: {},
    get filteredItems() {
        if (this.search === '') return this.items;
        return this.items.filter(item => 
            String(item.label).toLowerCase().includes(this.search.toLowerCase()) ||
            String(item.value).toLowerCase().includes(this.search.toLowerCase())
        );
    },
    get selectedLabel() {
        let selected = this.items.find(item => String(item.value) === String(this.value));
        return selected ? selected.label : '';
    },
    select(itemValue) {
        this.value = itemValue;
        this.open = false;
        this.search = '';
        this.$dispatch('change', itemValue);
    },
    updatePosition() {
        const rect = this.$refs.trigger.getBoundingClientRect();
        this.dropdownStyle = {
            position: 'fixed',
            top: (rect.bottom + 4) + 'px',
            left: rect.left + 'px',
            width: rect.width + 'px',
            zIndex: 9999
        };
    },
    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => this.updatePosition());
        }
    }
}" @click.outside="open = false" @scroll.window="if(open) updatePosition()" @resize.window="if(open) updatePosition()" class="relative">
    <flux:field>
        @if ($label) <flux:label>{{ $label }}</flux:label> @endif
        
        <div class="relative group" x-ref="trigger">
            <div class="relative">
                <input 
                    type="text"
                    readonly 
                    :value="selectedLabel" 
                    placeholder="{{ $placeholder }}"
                    @click="toggle()"
                    class="w-full cursor-pointer rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 pr-16 {{ $size === 'sm' ? 'py-1.5 text-xs' : '' }}"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                    <button type="button" x-show="value" x-on:click.stop="value = null; search = ''; $dispatch('change', null)" class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-700" x-cloak>
                        <svg class="size-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <svg class="size-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>
    </flux:field>

    <template x-teleport="body">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             :style="dropdownStyle"
             class="border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 border rounded-lg shadow-xl overflow-hidden"
             style="display: none;">
            
            <div class="p-2 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
                <flux:input 
                    x-model="search" 
                    placeholder="{{ $searchPlaceholder }}" 
                    size="sm"
                    autocomplete="off"
                    @keydown.escape.window="open = false"
                    @keydown.enter.prevent="if (filteredItems.length > 0) { select(filteredItems[0].value); }"
                    x-init="$watch('open', value => value && $nextTick(() => $el.focus()))"
                />
            </div>

            <div class="max-h-64 overflow-y-auto p-1">
                <template x-for="item in filteredItems" :key="item.value">
                    <div 
                        @click="select(item.value)"
                        class="px-3 py-2 text-sm rounded-md cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/50 flex items-center justify-between transition-colors"
                        :class="value == item.value ? 'bg-zinc-50 dark:bg-zinc-700 font-medium text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-300'"
                    >
                        <span x-text="item.label"></span>
                        <flux:icon.check x-show="value == item.value" class="size-4" x-cloak />
                    </div>
                </template>
                <div x-show="filteredItems.length === 0" class="px-3 py-6 text-sm text-zinc-400 text-center italic">
                    Không tìm thấy kết quả
                </div>
            </div>
        </div>
    </template>
</div>
