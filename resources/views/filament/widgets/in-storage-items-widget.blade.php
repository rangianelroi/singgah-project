<x-filament-widgets::widget>
    <x-filament-actions::modals />

    <x-filament::section>
        {{-- Header Widget --}}
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20 rounded-xl shadow-sm">
                            <x-heroicon-o-archive-box class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Barang di Gudang</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Siap dihubungi untuk pengambilan</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-gradient-to-r from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-900/20 text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-600/30 dark:ring-blue-400/40 shadow-sm">
                        <x-heroicon-m-archive-box class="w-5 h-5 mr-2" />
                        {{ count($inStorageItems) }} barang menunggu
                    </span>
                </div>
            </div>
        </x-slot>
        
        {{-- Konten Widget --}}
        @if(count($inStorageItems) > 0)
            <div class="flex flex-col gap-4">
                @forelse ($inStorageItems as $item)
                    <div class="group relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-row transform transition-all duration-300 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 hover:translate-x-1">
                        
                        {{-- Decorative accent --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/30 to-transparent dark:from-blue-500/10 dark:to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
                        
                        <div class="p-5 flex-grow relative z-10">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white shadow-md transform transition-all duration-200 group-hover:scale-105" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);">
                                    Di Gudang
                                </span>
                                
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs font-medium">
                                    <x-heroicon-s-map-pin class="w-3.5 h-3.5" />
                                    <span class="whitespace-nowrap">{{ $item->storage_location ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight">
                                {{ $item->item_name }}
                            </h3>
                            <div class="flex items-center gap-3 p-2.5 bg-gray-100/50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20">
                                    <x-heroicon-m-user class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Penumpang</p>
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                        {{ $item->passenger->full_name }}
                                    </p>
                                </div>
                            </div>

                            {{-- Menampilkan Log Komunikasi Terakhir --}}
                            @php
                                $latestComm = $item->communications->last();
                            @endphp

                            @if($latestComm)
                                <div class="mt-3 flex items-start gap-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30">
                                    <div class="mt-0.5">
                                        @if($latestComm->channel === 'whatsapp')
                                            <x-heroicon-m-chat-bubble-oval-left-ellipsis class="w-4 h-4 text-green-600 dark:text-green-400" />
                                        @elseif($latestComm->channel === 'email')
                                            <x-heroicon-m-envelope class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        @else
                                            <x-heroicon-m-phone class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 capitalize">
                                                {{ $latestComm->channel === 'other' ? 'Manual/Telp' : ucfirst($latestComm->channel) }}
                                            </span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ $latestComm->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 line-clamp-2">
                                            {{ $latestComm->message_summary }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                @if($item->latestStatusLog && $item->latestStatusLog->notes)
                                    <div class="mt-3 flex items-start gap-2 p-2.5 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700 text-xs text-gray-500">
                                        <x-heroicon-m-information-circle class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                        <span class="italic">{{ $item->latestStatusLog->notes }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Footer Aksi --}}
                        <div class="px-4 py-5 ml-auto bg-gradient-to-l from-gray-50 to-transparent dark:from-gray-800/60 dark:to-transparent border-l border-gray-100 dark:border-gray-700 flex flex-col gap-2 justify-center">
                            <x-filament::button
                                color="success"
                                size="sm"
                                tag="a"
                                :href="$this->getWhatsAppUrl($item)"
                                :disabled="empty($item->passenger->phone_number)"
                                target="_blank"
                                icon="heroicon-o-chat-bubble-left-right"
                                class="shadow-md hover:shadow-lg transition-all duration-200"
                            >
                                Chat WA
                            </x-filament::button>

                            <x-filament::button
                                color="gray"
                                size="sm"
                                wire:click="mountAction('manualLogAction', { record: {{ $item->id }} })"
                                icon="heroicon-o-pencil-square"
                                class="shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                Catat
                            </x-filament::button>

                            <x-filament::button
                                color="info"
                                size="sm"
                                wire:click="mountAction('startCommunication', { record: {{ $item->id }} })"
                                icon="heroicon-o-play-circle"
                                class="shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                Proses
                            </x-filament::button>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        @else
            {{-- Tampilan Kosong --}}
            <div class="relative py-16">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-64 h-64 bg-gradient-to-br from-blue-100/20 to-transparent dark:from-blue-500/10 dark:to-transparent rounded-full blur-3xl -z-10"></div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/20 mb-6 ring-2 ring-blue-200 dark:ring-blue-800/30">
                        <x-heroicon-o-inbox class="w-10 h-10 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Tidak Ada Barang di Gudang
                    </h3>
                    <p class="text-base text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Tidak ada barang yang perlu dihubungi saat ini.
                    </p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>