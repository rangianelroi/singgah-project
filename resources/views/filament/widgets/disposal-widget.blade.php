<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-medium">Barang Siap Dimusnahkan ({{ count($itemsForDisposal) }})</h2>
        <p class="text-sm text-gray-500">Daftar barang yang telah melewati batas waktu penyimpanan.</p>
        
        <div class="mt-4 space-y-3">
            @forelse ($itemsForDisposal as $item)
                <div class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-800">
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">
                                Disita tgl: {{ $item->confiscation_date->format('d M Y') }} 
                                ({{ $item->confiscation_date->diffForHumans() }})
                            </p>
                        </div>
                        
                        <x-filament::button
                            color="danger"
                            icon="heroicon-o-fire"
                            wire:click="mountAction('processDisposalAction', { record: '{{ $item->id }}' })"
                        >
                            Proses
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada barang yang melewati batas waktu penyimpanan.</p>
            @endforelse
        </div>
        
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>