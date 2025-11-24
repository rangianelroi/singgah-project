<div class="rounded-xl bg-gradient-to-br from-primary-800 to-primary-700 text-white shadow-lg p-6">
    <div class="flex items-center justify-between">
        {{-- Kolom Kiri: Teks Sambutan & Tombol --}}
        <div>
            <h2 class="text-2xl font-bold tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-1 text-primary-200">
                Siap untuk memulai tugas hari ini?
            </p>
            <div class="mt-6">
                <x-filament::button
                    icon="heroicon-o-plus-circle"
                    tag="a"
                    href="{{ \App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource::getUrl('create') }}"
                    color="white"
                >
                    Catat Barang Sitaan Baru
                </x-filament::button>
            </div>
        </div>

        {{-- Kolom Kanan: Statistik Ringkas --}}
        <div class="text-right">
            <p class="text-sm font-medium text-primary-200">Barang Dicatat Hari Ini</p>
            <p class="text-5xl font-extrabold">
                {{ \App\Models\ConfiscatedItem::whereDate('created_at', today())->count() }}
            </p>
        </div>
    </div>
</div>