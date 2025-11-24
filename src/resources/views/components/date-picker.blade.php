@props(['disabled' => false])

<div
    x-data="{
        value: @entangle($attributes->wire('model')),
        instance: null,
        init() {
            this.instance = flatpickr(this.$refs.input, {
                dateFormat: 'd/m/Y',
                defaultDate: this.value ? new Date(this.value) : null,
                locale: window.flatpickrVN,
                allowInput: true,
                onChange: (selectedDates, dateStr, instance) => {
                    if (selectedDates.length > 0) {
                        const date = selectedDates[0];
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        this.value = `${year}-${month}-${day}`;
                    } else {
                        this.value = null;
                    }
                }
            });

            this.$watch('value', value => {
                if (value) {
                    this.instance.setDate(new Date(value), false);
                } else {
                    this.instance.clear();
                }
            });
        }
    }"
    wire:ignore
>
    <flux:input
        x-ref="input"
        type="text"
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        :disabled="$disabled"
        placeholder="dd/mm/yyyy"
    />
</div>