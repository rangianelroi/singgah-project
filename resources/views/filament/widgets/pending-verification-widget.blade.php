<x-filament-widgets::widget>
    {{-- PENTING: Tambahkan ini agar Pop-up Wizard bisa muncul --}}
    <x-filament-actions::modals />

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <div class="flex-shrink-0">
                        <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                            <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-primary-500" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Barang Menunggu Verifikasi</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daftar barang yang memerlukan verifikasi dari petugas</p>
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

        @if($this->getItems()->isNotEmpty())
            {{-- SEARCH BAR --}}
            <div class="mb-6">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari berdasarkan nama barang atau nama penumpang..."
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
            </div>

            {{-- SELECT ALL & BULK ACTION --}}
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        wire:click="toggleSelectAll"
                        @checked(count($selectedItems) === count($this->getItems()) && count($this->getItems()) > 0)
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                    />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer" wire:click="toggleSelectAll">
                        Pilih Semua ({{ count($selectedItems) }}/{{ count($this->getItems()) }})
                    </span>
                </div>

                @if(count($selectedItems) > 0)
                    <button 
                        wire:click="submitToStorage"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                        <x-heroicon-m-archive-box class="w-5 h-5" />
                        Masuk Gudang ({{ count($selectedItems) }})
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->getItems() as $item)
                    <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col transform transition-all duration-200 hover:shadow-md hover:border-primary-300 dark:hover:border-primary-600">
                        
                        {{-- CHECKBOX --}}
                        <div class="absolute top-4 left-4 z-10">
                            <input 
                                type="checkbox" 
                                value="{{ $item->id }}" 
                                wire:model.live="selectedItems"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer w-5 h-5"
                            />
                        </div>

                        <div class="p-6 flex-grow pt-12">
                            {{-- Header dengan Category dan Time --}}
                            <div class="flex items-start justify-between gap-3 mb-5">
                                <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold text-white shadow-sm"
                                      style="background-color: {{ \App\Models\ConfiscatedItem::getCategoryColor($item->category) }};">
                                    {{ str_replace('_', ' ', Str::title(strtolower($item->category))) }}
                                </span>
                                
                                <div class="inline-flex items-center text-gray-500 dark:text-gray-400 text-xs">
                                    <x-heroicon-s-clock class="w-3.5 h-3.5 mr-1" />
                                    <span class="whitespace-nowrap">{{ $item->created_at->diffForHumans(['short' => true]) }}</span>
                                </div>
                            </div>

                            {{-- Item Name dan Quantity --}}
                            <div class="pb-5 mb-5 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                    {{ $item->item_name }}
                                </h3>
                                @if($item->item_quantity)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        <span class="font-medium">{{ $item->item_quantity }}</span>
                                        <span class="ml-1">{{ $item->item_unit }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Passenger dan Flight Info --}}
                            <div class="space-y-4">
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

                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                        <x-heroicon-m-paper-airplane class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-0.5">Penerbangan</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $item->flight->airline->code ?? '' }} {{ $item->flight->flight_number ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="px-6 py-4 mt-auto bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                
                                {{-- PERBAIKAN UTAMA: Gunakan wire:click manual untuk memicu action dengan ID spesifik --}}
                                <x-filament::button
                                    color="success"
                                    icon="heroicon-m-check-circle"
                                    class="flex-1"
                                    wire:click="mountAction('approveAction', { record: {{ $item->id }} })"
                                >
                                    Verifikasi
                                </x-filament::button>

                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    tag="a"
                                    href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('view', ['record' => $item]) }}"
                                    target="_blank"
                                    icon="heroicon-m-eye"
                                    outlined
                                    class="flex-1"
                                >
                                    Detail
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $this->getItems()->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                    Tidak Ada Item Menunggu
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Semua barang sitaan sudah diverifikasi
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>