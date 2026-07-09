<x-shopper::container class="py-5">
    <x-shopper::heading :title="__('Stock alerts')" />

    <div class="mt-10">
        {{ $this->table }}
    </div>
</x-shopper::container>
