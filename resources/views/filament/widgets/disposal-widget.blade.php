<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header Widget --}}
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/20 rounded-xl shadow-sm">
                            <x-heroicon-o-fire class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Barang Siap Dimusnahkan</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu persetujuan Dept. Head</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-gradient-to-r from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-900/20 text-red-700 dark:text-red-300 ring-1 ring-inset ring-red-600/30 dark:ring-red-400/40 shadow-sm">
                        <x-heroicon-m-fire class="w-5 h-5 mr-2" />
                        {{ count($itemsForDisposal) }} item menunggu
                    </span>
                </div>
            </div>
        </x-slot>

        {{-- Konten Widget --}}
        @if(count($itemsForDisposal) > 0)
            {{-- [BARU] Tombol Select All --}}
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" 
                       wire:click="toggleSelectAll"
                       class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                       @checked(count($selectedItems) === count($itemsForDisposal) && count($itemsForDisposal) > 0)>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 cursor-pointer" wire:click="toggleSelectAll">
                    Pilih Semua ({{ count($selectedItems) }}/{{ count($itemsForDisposal) }})
                </span>
            </div>

            <div class="flex flex-col gap-4">
                @forelse ($itemsForDisposal as $item)
                    <div class="group relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-row transform transition-all duration-300 hover:shadow-lg hover:border-red-300 dark:hover:border-red-600 hover:translate-x-1">
                        
                        {{-- Decorative accent --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-100/30 to-transparent dark:from-red-500/10 dark:to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
                        
                        {{-- [BARU] Checkbox Selection --}}
                        <div class="p-5 flex items-center relative z-10">
                            <input type="checkbox" 
                                   value="{{ $item->id }}" 
                                   wire:model.live="selectedItems"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-5 h-5">
                        </div>

                        <div class="p-5 flex-grow relative z-10">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white shadow-md transform transition-all duration-200 group-hover:scale-105" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);">
                                    {{ str_replace('_', ' ', Str::title(strtolower($item->category))) }}
                                </span>
                                
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs font-medium">
                                    <x-heroicon-s-calendar class="w-3.5 h-3.5" />
                                    <span class="whitespace-nowrap">{{ $item->confiscation_date->format('d M Y') }}</span>
                                </div>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight">
                                {{ $item->item_name }}
                            </h3>
                            <div class="flex items-center gap-3 p-2.5 bg-gray-100/50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/20">
                                    <x-heroicon-m-calendar class="w-4 h-4 text-red-600 dark:text-red-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Tersimpan Sejak</p>
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                        {{ $item->confiscation_date->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Aksi --}}
                        <div class="px-4 py-5 ml-auto bg-gradient-to-l from-gray-50 to-transparent dark:from-gray-800/60 dark:to-transparent border-l border-gray-100 dark:border-gray-700 flex flex-col gap-2 justify-center">
                            <x-filament::button
                                color="danger"
                                size="sm"
                                icon="heroicon-o-fire"
                                wire:click="mountAction('processDisposalAction', { record: '{{ $item->id }}' })"
                                class="shadow-md hover:shadow-lg transition-all duration-200"
                            >
                                Proses
                            </x-filament::button>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>

            {{-- [BARU] Tombol Bulk Action muncul jika ada item dipilih --}}
            @if(count($selectedItems) > 0)
                <div class="mt-6 flex justify-end">
                    {{ ($this->processBulkDisposalAction)(['class' => 'shadow-lg']) }}
                </div>
            @endif
        @else
            {{-- Tampilan Kosong --}}
            <div class="relative py-16">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-64 h-64 bg-gradient-to-br from-red-100/20 to-transparent dark:from-red-500/10 dark:to-transparent rounded-full blur-3xl -z-10"></div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-900/20 mb-6 ring-2 ring-red-200 dark:ring-red-800/30">
                        <x-heroicon-o-check-circle class="w-10 h-10 text-red-600 dark:text-red-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Semua Aman!
                    </h3>
                    <p class="text-base text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Tidak ada barang yang pending untuk dimusnahkan saat ini.
                    </p>
                </div>
            </div>
        @endif
        
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>