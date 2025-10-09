<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-medium">Menunggu Konfirmasi Pengiriman ({{ count($itemsForConfirmation) }})</h2>
        <p class="text-sm text-gray-500">Barang yang menunggu persetujuan akhir dan detail pengiriman dari penumpang.</p>
        
        <div class="mt-4 space-y-3">
            @forelse ($itemsForConfirmation as $item)
                <div class="p-4 rounded-xl shadow-sm bg-yellow-50 dark:bg-yellow-800/20 ring-1 ring-yellow-200 dark:ring-yellow-900">
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $item->passenger->full_name }}</p>
                        </div>
                        
                        {{-- Grup Tombol Aksi untuk Tahap Konfirmasi --}}
                        <div class="flex items-center gap-2">
                            {{-- Aksi Batal (Paling Kiri) --}}
                            <x-filament::button
                                color="danger"
                                wire:click="mountAction('cancelShipmentProcessAction', { record: '{{ $item->id }}' })"
                                outlined
                            >
                                Batalkan
                            </x-filament::button>

                            {{-- Aksi Follow Up & Catat Log --}}
                            <x-filament::button color="gray" tag="a" :href="$this->getWhatsAppUrl($item)" target="_blank" icon="heroicon-o-chat-bubble-left-right">
                                Follow Up
                            </x-filament::button>
                            <x-filament::button color="gray" wire:click="mountAction('logResponseAction', { record: '{{ $item->id }}' })" icon="heroicon-o-pencil-square">
                                Catat Respon
                            </x-filament::button>

                            {{-- Aksi Utama (Paling Kanan) --}}
                            <x-filament::button color="success" wire:click="mountAction('confirmShipmentAction', { record: '{{ $item->id }}' })" icon="heroicon-o-truck">
                                Konfirmasi & Kirim
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Tidak ada barang yang menunggu konfirmasi.</p>
            @endforelse
        </div>
        
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>