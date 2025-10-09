<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Judul Widget --}}
        <h2 class="text-lg font-medium">Tugas Gudang ({{ count($itemsForStorage) }})</h2>
        <p class="text-sm text-gray-500">Daftar barang yang perlu dimasukkan ke gudang.</p>
        
        {{-- Daftar Barang --}}
        <div class="mt-4 space-y-3">
            @forelse ($itemsForStorage as $item)
                <div class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-800">
                    <div class="flex justify-between items-center gap-4">
                        {{-- Info Barang --}}
                        <div>
                            <p class="font-semibold">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $item->passenger->full_name }} ({{ $item->category }})</p>
                        </div>
                        
                        {{-- Tombol Aksi --}}
                        <x-filament::button
                            color="primary"
                            wire:click="mountAction('storeItemAction', { record: '{{ $item->id }}' })"
                        >
                            Proses
                        </x-filament::button>

                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada barang yang perlu diproses ke gudang.</p>
            @endforelse
        </div>
        
        {{-- Komponen wajib untuk menampilkan modal Aksi --}}
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>