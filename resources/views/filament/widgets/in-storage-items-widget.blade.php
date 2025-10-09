<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-medium">Barang di Gudang (Siap Dihubungi) ({{ count($inStorageItems) }})</h2>
        <p class="text-sm text-gray-500">Daftar barang yang siap untuk dihubungi penumpangnya.</p>
        
        <div class="mt-4 space-y-3">
            @forelse ($inStorageItems as $item)
                <div class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-800">
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $item->passenger->full_name }} (Lokasi: {{ $item->storage_location ?? 'N/A' }})</p>
                        </div>
                        
                        {{-- Grup Tombol Aksi --}}
                        <div class="flex items-center gap-2">
                            {{-- Aksi Sekunder 1: Chat WA Manual --}}
                            <x-filament::button
                                color="gray"
                                tag="a"
                                :href="$this->getWhatsAppUrl($item)"
                                :disabled="empty($item->passenger->phone_number)"
                                target="_blank"
                                icon="heroicon-o-chat-bubble-left-right"
                            >
                                Chat WA
                            </x-filament::button>

                            {{-- Aksi Sekunder 2: Catat Log Manual --}}
                            <x-filament::button
                                color="gray"
                                wire:click="mountAction('manualLogAction', { record: '{{ $item->id }}' })"
                                icon="heroicon-o-pencil-square"
                            >
                                Catat Log
                            </x-filament::button>

                            {{-- Aksi Utama: Memulai Proses --}}
                            <x-filament::button
                                color="info"
                                wire:click="mountAction('startCommunicationAction', { record: '{{ $item->id }}' })"
                                icon="heroicon-o-play-circle"
                            >
                                Mulai Proses
                            </x-filament::button>
                        </div>

                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada barang yang perlu dihubungi saat ini.</p>
            @endforelse
        </div>
        
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>