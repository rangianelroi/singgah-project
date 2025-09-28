<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-center p-8">
            <a href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('create') }}"
               class="flex flex-col items-center justify-center p-6 bg-primary-600 text-white rounded-full hover:bg-primary-500 transition-colors duration-200 w-48 h-48">
                <x-heroicon-o-plus class="w-16 h-16" />
                <span class="mt-2 text-lg font-semibold">Catat Barang Baru</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>