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
                                wire:click="openPickupModal({{ $latestPickup->id }})"
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

    {{-- Modal Detail Kerabat --}}
    @if($showPickupModal && $selectedPickup)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-75 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full max-h-96 overflow-y-auto">
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Detail Kerabat: {{ $selectedPickup->pickup_by_name }}
                </h3>
                <button 
                    wire:click="closePickupModal"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                >
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6 space-y-6">
                {{-- Data Penjemput --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-4">Data Penjemput</h4>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Nama</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $selectedPickup->pickup_by_name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">No. Identitas</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $selectedPickup->pickup_by_identity_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Hubungan dengan Penumpang</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $selectedPickup->relationship_to_passenger }}</p>
                        </div>

                        @if($selectedPickup->pickup_timestamp)
                        <div>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Waktu Pengambilan</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $selectedPickup->pickup_timestamp->format('d M Y - H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Foto Penerima --}}
                @if($selectedPickup->photo_of_recipient_path)
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Foto Penerima</h4>
                    <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img 
                            src="{{ asset('storage/' . $selectedPickup->photo_of_recipient_path) }}" 
                            alt="Foto Penerima" 
                            class="w-full h-auto max-h-64 object-contain bg-gray-100 dark:bg-gray-900"
                        />
                    </div>
                </div>
                @endif

                {{-- Foto Identitas --}}
                @if($selectedPickup->photo_of_identity_path)
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Foto Identitas</h4>
                    <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img 
                            src="{{ asset('storage/' . $selectedPickup->photo_of_identity_path) }}" 
                            alt="Foto Identitas" 
                            class="w-full h-auto max-h-64 object-contain bg-gray-100 dark:bg-gray-900"
                        />
                    </div>
                </div>
                @endif

                @if(!$selectedPickup->photo_of_recipient_path && !$selectedPickup->photo_of_identity_path)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm text-yellow-800 dark:text-yellow-300">
                        <strong>Catatan:</strong> Belum ada foto dokumentasi untuk kerabat ini.
                    </p>
                </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
                <x-filament::button
                    color="gray"
                    wire:click="closePickupModal"
                >
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </div>
    @endif
</x-filament-widgets::widget>