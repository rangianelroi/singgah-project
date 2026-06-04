<x-filament-widgets::widget>
    {{-- Wajib ada untuk modal form --}}
    <x-filament-actions::modals />

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <x-heroicon-o-user-group class="w-5 h-5 text-orange-500" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menunggu Pengambilan (Kerabat)</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Konfirmasi serah terima barang kepada kerabat yang ditunjuk</p>
                    </div>
                </div>
                
                @if($this->getItems()->total() > 0)
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 ring-1 ring-inset ring-orange-600/20 dark:ring-orange-400/30">
                        {{ $this->getItems()->total() }} siap diambil
                    </span>
                </div>
                @endif
            </div>
        </x-slot>

        {{-- Search Input --}}
        <div class="mb-6">
            <div class="relative">
                <x-heroicon-m-magnifying-glass class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 dark:text-gray-500 pointer-events-none" />
                <input 
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama barang atau penumpang..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
            </div>
        </div>

        @if($this->getItems()->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->getItems() as $item)
                    <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
                        
                        <div class="p-6 flex-grow">
                            {{-- Nama Barang --}}
                            <div class="pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white line-clamp-2">
                                    {{ $item->item_name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Milik: <span class="font-medium">{{ $item->passenger->full_name ?? '-' }}</span>
                                </p>
                            </div>

                            {{-- Info Penjemput (Highlight Bagian Ini) --}}
                            <div class="bg-orange-50 dark:bg-orange-900/10 rounded-lg p-3 mb-4 border border-orange-100 dark:border-orange-900/20">
                                <p class="text-xs font-medium text-orange-800 dark:text-orange-400 mb-2 uppercase tracking-wider">
                                    Data Penjemput
                                </p>
                                {{-- Ambil pickup record terbaru (latest) --}}
                                @php
                                    $pickupData = $item->pickups->sortByDesc('created_at')->first();
                                @endphp
                                @if($pickupData)
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <x-heroicon-m-identification class="w-8 h-8 text-orange-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ $pickupData->pickup_by_name ?? 'Belum ada data' }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                            ID: {{ $pickupData->pickup_by_identity_number ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate italic">
                                            Hub: {{ $pickupData->relationship_to_passenger ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                @else
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada data penjemput</p>
                                @endif
                            </div>

                            {{-- Tanggal Verifikasi --}}
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <x-heroicon-m-clock class="w-4 h-4 mr-1.5" />
                                Menunggu sejak: {{ $item->updated_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-700">
                             <x-filament::button
                                color="primary"
                                icon="heroicon-m-hand-raised"
                                class="w-full mb-2"
                                wire:click="mountAction('confirmHandoverAction', { record: {{ $item->id }} })"
                            >
                                Konfirmasi Serah Terima
                            </x-filament::button>

                            @php
                                $latestPickup = $item->pickups->sortByDesc('created_at')->first();
                            @endphp
                            @if($latestPickup)
                            <x-filament::button
                                color="info"
                                icon="heroicon-o-information-circle"
                                class="w-full"
                                wire:click="mountAction('viewRecipientDetailsAction', { record: {{ $item->id }}, pickup: {{ $latestPickup->id }} })"
                            >
                                Lihat Detail & Foto Kerabat
                            </x-filament::button>
                            @else
                            <div class="text-center text-xs text-gray-500 py-2">
                                Belum ada data kerabat
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $this->getItems()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        @if($this->search)
                            <x-heroicon-o-magnifying-glass class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                        @else
                            <x-heroicon-o-inbox class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                        @endif
                    </div>
                </div>
                
                @if($this->search)
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Pencarian tidak menemukan hasil
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                        Tidak ada barang dengan nama "<span class="font-semibold">{{ $this->search }}</span>" atau penumpang yang cocok
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                        <button 
                            type="button"
                            wire:click="clearSearch"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 bg-primary-50 hover:bg-primary-100 dark:bg-primary-900/20 dark:hover:bg-primary-900/30 rounded-lg transition"
                        >
                            <x-heroicon-m-x-mark class="w-4 h-4 mr-1.5" />
                            Bersihkan Pencarian
                        </button>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        Tidak ada barang yang sedang menunggu pengambilan kerabat.
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Semua barang sudah diproses atau belum ada yang memasuki status ini.
                    </p>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>