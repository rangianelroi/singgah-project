<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header Widget --}}
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20 rounded-xl shadow-sm">
                            <x-heroicon-o-archive-box-arrow-down class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tugas Gudang</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Barang yang siap dimasukkan ke gudang</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-gradient-to-r from-primary-100 to-primary-50 dark:from-primary-900/30 dark:to-primary-900/20 text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/30 dark:ring-primary-400/40 shadow-sm">
                        <x-heroicon-m-queue-list class="w-5 h-5 mr-2" />
                        {{ $this->getItems()->total() }} item menunggu
                    </span>
                </div>
            </div>
        </x-slot>

        {{-- Konten Widget --}}
        @if($this->getItems()->isNotEmpty())
            <div class="flex flex-col gap-4">
                @foreach($this->getItems() as $item)
                    <div class="group relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-row transform transition-all duration-300 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 hover:translate-x-1">
                        
                        {{-- Decorative accent --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-100/30 to-transparent dark:from-primary-500/10 dark:to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
                        
                        <div class="p-5 flex-grow relative z-10">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-black shadow-md transform transition-all duration-200 group-hover:scale-105"
                                      style="background: linear-gradient(135deg, {{ \App\Models\ConfiscatedItem::getCategoryColor($item->category) }} 0%, {{ \App\Models\ConfiscatedItem::getCategoryColor($item->category) }}dd 100%); box-shadow: 0 4px 12px {{ \App\Models\ConfiscatedItem::getCategoryColor($item->category) }}40;">
                                    {{ str_replace('_', ' ', Str::title(strtolower($item->category))) }}
                                </span>
                                
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs font-medium">
                                    <x-heroicon-s-clock class="w-3.5 h-3.5" />
                                    <span class="whitespace-nowrap">{{ $item->created_at->diffForHumans(['short' => true]) }}</span>
                                </div>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight">
                                {{ $item->item_name }}
                            </h3>
                            <div class="flex items-center gap-3 p-2.5 bg-gray-100/50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20">
                                    <x-heroicon-m-user class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Penumpang</p>
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                        {{ $item->passenger->full_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Aksi --}}
                        <div class="px-4 py-5 ml-auto bg-gradient-to-l from-gray-50 to-transparent dark:from-gray-800/60 dark:to-transparent border-l border-gray-100 dark:border-gray-700 flex flex-col gap-2 justify-center">
                            {{-- ========================================== --}}
                            {{-- PERBAIKAN: Cara memanggil Aksi yang benar --}}
                            {{-- ========================================== --}}
                            <x-filament::button
                                color="primary"
                                size="sm"
                                icon="heroicon-m-archive-box-arrow-down"
                                wire:click="mountAction('storeItem', { record: '{{ $item->id }}' })"
                                class="shadow-md hover:shadow-lg transition-all duration-200"
                            >
                                Proses
                            </x-filament::button>

                            <x-filament::button
                                color="gray" size="sm" tag="a"
                                href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('view', ['record' => $item]) }}"
                                target="_blank" icon="heroicon-m-eye" outlined
                                class="shadow-sm hover:shadow-md transition-all duration-200">
                                Detail
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $this->getItems()->links() }}
            </div>
        @else
            {{-- Tampilan Kosong --}}
            <div class="relative py-16">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-64 h-64 bg-gradient-to-br from-primary-100/20 to-transparent dark:from-primary-500/10 dark:to-transparent rounded-full blur-3xl -z-10"></div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-900/20 mb-6 ring-2 ring-primary-200 dark:ring-primary-800/30">
                        <x-heroicon-o-check-circle class="w-10 h-10 text-primary-600 dark:text-primary-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Tidak Ada Tugas Gudang
                    </h3>
                    <p class="text-base text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Semua barang sudah diproses ke gudang. Pekerjaan Anda selesai! 👏
                    </p>
                </div>
            </div>
        @endif
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>