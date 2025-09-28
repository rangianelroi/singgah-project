<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Tugas Verifikasi Barang ({{ count($pendingItems) }})
        </h2>
        
        <div class="mt-4 space-y-3">
            @forelse ($pendingItems as $item)
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $item->passenger->full_name }} pada {{ $item->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            {{ $this->approveAction->record($item) }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada tugas verifikasi saat ini. Kerja bagus!</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>