<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-medium">Tugas Verifikasi Barang ({{ count($pendingItems) }})</h2>
        <div class="mt-4 space-y-3">
            @forelse ($pendingItems as $item)
                <div class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-800">
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $item->passenger->full_name }}</p>
                        </div>
                        
                        {{-- UBAH BAGIAN INI: dari <a> menjadi button yang memanggil Aksi --}}
                        <x-filament::button
                            color="primary"
                            wire:click="mountAction('approveAction', { record: '{{ $item->id }}' })"
                        >
                            Proses
                        </x-filament::button>

                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada tugas verifikasi.</p>
            @endforelse
        </div>
        
        {{-- Tambahkan ini di akhir untuk merender modal --}}
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>