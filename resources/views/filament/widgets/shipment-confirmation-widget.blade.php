<x-filament-widgets::widget>
    {{-- PENTING: Tag ini wajib ada agar Modal muncul --}}
    <x-filament-actions::modals />

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <x-heroicon-o-truck class="w-5 h-5 text-yellow-600" />
                </div>
                <div>
                    <h2 class="text-lg font-semibold">Konfirmasi Pengiriman</h2>
                    <p class="text-sm text-gray-500">Barang yang sedang menunggu detail pengiriman</p>
                </div>
            </div>
        </x-slot>

        @if($this->getItems()->count() > 0)
            <div class="space-y-4 mt-4">
                @foreach($this->getItems() as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            {{-- Info Barang --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-1 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800">
                                        {{ $item->item_name }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $item->passenger->full_name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->passenger->phone_number ?? 'No Phone' }}
                                </p>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="flex flex-wrap items-center gap-2">
                                {{-- Tombol WA --}}
                                <x-filament::button
                                    color="success"
                                    tag="a"
                                    icon="heroicon-o-chat-bubble-left-right"
                                    href="{{ $this->getWhatsAppUrl($item) }}"
                                    target="_blank"
                                    size="sm"
                                    outlined
                                >
                                    Chat WA
                                </x-filament::button>

                                {{-- Tombol Catat Respon --}}
                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    icon="heroicon-o-pencil-square"
                                    wire:click="mountAction('logResponseAction', { record: {{ $item->id }} })"
                                >
                                    Respon
                                </x-filament::button>

                                {{-- Tombol Konfirmasi & Kirim --}}
                                <x-filament::button
                                    color="primary"
                                    size="sm"
                                    icon="heroicon-o-truck"
                                    wire:click="mountAction('confirmShipmentAction', { record: {{ $item->id }} })"
                                >
                                    Kirim
                                </x-filament::button>
                                
                                {{-- Tombol Batal --}}
                                <x-filament::button
                                    color="danger"
                                    size="sm"
                                    icon="heroicon-o-x-mark"
                                    wire:click="mountAction('cancelShipmentProcessAction', { record: {{ $item->id }} })"
                                    tooltip="Batalkan Proses"
                                >
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $this->getItems()->links() }}
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                Tidak ada barang menunggu konfirmasi pengiriman.
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>