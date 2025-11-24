<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header Widget --}}
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                            <x-heroicon-o-archive-box-arrow-down class="w-5 h-5 text-primary-500" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tugas Gudang</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Barang yang siap dimasukkan ke gudang</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 ring-1 ring-inset ring-primary-600/20 dark:ring-primary-400/30">
                        <x-heroicon-m-queue-list class="w-4 h-4 mr-1.5" />
                        {{ $this->getItems()->total() }} item menunggu
                    </span>
                </div>
            </div>
        </x-slot>

        {{-- Konten Widget --}}
        @if($this->getItems()->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->getItems() as $item)
                    <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col transform transition-all duration-200 hover:shadow-md hover:border-primary-300 dark:hover:border-primary-600">
                        
                        <div class="p-6 flex-grow">
                            {{-- ... (Isi Kartu lainnya sudah benar) ... --}}
                            <div class="flex items-start justify-between gap-3 mb-5">
                                <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold text-white shadow-sm"
                                      style="background-color: {{ \App\Models\ConfiscatedItem::getCategoryColor($item->category) }};">
                                    {{ str_replace('_', ' ', Str::title(strtolower($item->category))) }}
                                </span>
                                
                                <div class="inline-flex items-center text-gray-500 dark:text-gray-400 text-xs">
                                    <x-heroicon-s-clock class="w-3.5 h-3.5 mr-1" />
                                    <span class="whitespace-nowrap">{{ $item->created_at->diffForHumans(['short' => true]) }}</span>
                                D</div>
                            </div>
                            <div class="pb-5 mb-5 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                    {{ $item->item_name }}
                                </h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                    <x-heroicon-m-user class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Penumpang</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $item->passenger->full_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Aksi --}}
                        <div class="px-6 py-4 mt-auto bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                {{-- ========================================== --}}
                                {{-- PERBAIKAN: Cara memanggil Aksi yang benar --}}
                                {{-- ========================================== --}}
                                <x-filament::button
                                    color="primary"
                                    size="sm"
                                    icon="heroicon-m-archive-box-arrow-down"
                                    wire:click="mountAction('storeItemAction', { record: '{{ $item->id }}' })"
                                    class="flex-1"
                                >
                                    Proses ke Gudang
                                </x-filament::button>

                                <x-filament::button
                                    color="gray" size="sm" tag="a"
                                    href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('view', ['record' => $item]) }}"
                                    target="_blank" icon="heroicon-m-eye" outlined>
                                    Detail
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $this->getItems()->links() }}
            </div>
        @else
            {{-- Tampilan Kosong --}}
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                    Tidak Ada Tugas Gudang
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Semua barang sudah diproses ke gudang.
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>