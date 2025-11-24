<x-filament-widgets::widget>
    <x-filament-actions::modals />

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-heroicon-o-archive-box class="w-5 h-5 text-blue-600" />
                </div>
                <div>
                    <h2 class="text-lg font-semibold">Barang di Gudang (Siap Dihubungi)</h2>
                    <p class="text-sm text-gray-500">Total: {{ count($inStorageItems) }} barang</p>
                </div>
            </div>
        </x-slot>
        
        <div class="mt-4 space-y-3">
            @forelse ($inStorageItems as $item)
                <div class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $item->item_name }}</p>
                            <p class="text-sm text-gray-500 mb-2">
                                Oleh: {{ $item->passenger->full_name }} 
                                <span class="mx-1">•</span>
                                Lokasi: {{ $item->storage_location ?? 'N/A' }}
                            </p>

                            {{-- BAGIAN BARU: Menampilkan Log Komunikasi Terakhir --}}
                            @php
                                // Ambil komunikasi terakhir dari koleksi (jika ada)
                                $latestComm = $item->communications->last();
                            @endphp

                            @if($latestComm)
                                <div class="mt-2 flex items-start gap-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30">
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
                                {{-- Fallback: Jika belum ada komunikasi, tampilkan status log (Opsional) --}}
                                @if($item->latestStatusLog && $item->latestStatusLog->notes)
                                    <div class="mt-2 flex items-start gap-2 p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700 text-xs text-gray-500">
                                        <x-heroicon-m-information-circle class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                        <span class="italic">{{ $item->latestStatusLog->notes }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        {{-- Grup Tombol Aksi --}}
                        <div class="flex items-center gap-2 flex-wrap self-start md:self-center">
                            <x-filament::button
                                color="success"
                                size="sm"
                                tag="a"
                                :href="$this->getWhatsAppUrl($item)"
                                :disabled="empty($item->passenger->phone_number)"
                                target="_blank"
                                icon="heroicon-o-chat-bubble-left-right"
                                outlined
                            >
                                Chat WA
                            </x-filament::button>

                            <x-filament::button
                                color="gray"
                                size="sm"
                                wire:click="mountAction('manualLogAction', { record: {{ $item->id }} })"
                                icon="heroicon-o-pencil-square"
                            >
                                Catat Log
                            </x-filament::button>

                            <x-filament::button
                                color="info"
                                size="sm"
                                wire:click="mountAction('startCommunication', { record: {{ $item->id }} })"
                                icon="heroicon-o-play-circle"
                            >
                                Mulai Proses
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-500">Tidak ada barang yang perlu dihubungi saat ini.</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>