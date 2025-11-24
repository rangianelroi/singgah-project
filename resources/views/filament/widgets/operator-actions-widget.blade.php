<x-filament-widgets::widget>
    <div class="rounded-xl bg-gradient-to-br from-primary-800 to-primary-700 text-white shadow-lg">
        <div class="p-6">
            {{-- Bagian Teks Sambutan --}}
            <div>
                <h2 class="text-2xl font-bold tracking-tight">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h2>
                <p class="mt-2 text-lg text-primary-200">
                    Siap untuk memulai? Klik tombol di bawah untuk mencatat barang sitaan baru.
                </p>
            </div>

            {{-- Bagian Tombol Aksi Kustom --}}
            <div class="mt-8">
                <a href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('create') }}"
                   class="inline-flex items-center justify-center gap-x-2 rounded-lg bg-white px-4 py-2 text-base font-semibold text-primary-600 shadow-sm transition-colors duration-75 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:focus:ring-offset-gray-800">

                    <x-heroicon-o-plus-circle class="h-5 w-5" />

                    <span>Catat Barang Sitaan Baru</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>