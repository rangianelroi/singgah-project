<div class="mb-8">
    {{-- Main Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 dark:from-primary-900/50 dark:to-primary-900/20 px-8 py-12 shadow-xl">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full -mr-48 -mt-48 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-primary-400/10 dark:bg-primary-400/5 rounded-full -ml-40 -mb-40 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-white/20 dark:bg-white/10 rounded-xl backdrop-blur-sm">
                    <x-heroicon-o-hand-raised class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p class="text-primary-100 mt-1">Squad Leader | Ringkasan Barang Sitaan</p>
                </div>
            </div>
            <p class="text-primary-50 text-base leading-relaxed max-w-2xl">
                Berikut adalah ringkasan lengkap status barang sitaan berdasarkan log terakhir. Monitor progress dan pastikan semua proses berjalan sesuai prosedur.
            </p>
        </div>
    </div>

    {{-- Statistics Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        
        {{-- Card 1: Menunggu Verifikasi --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100 dark:bg-blue-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Menunggu Verifikasi</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->where('status', 'pending_verification');
                                    })->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20 rounded-xl">
                            <x-heroicon-o-clipboard-document-check class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang menunggu proses verifikasi dari petugas</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Siap Diambil --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600 to-orange-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-100 dark:bg-orange-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Siap Diambil</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->where('status', 'ready_for_pickup');
                                    })->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-900/20 rounded-xl">
                            <x-heroicon-o-shopping-bag class="w-8 h-8 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang sudah siap untuk diambil penumpang</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Dalam Proses --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-100 dark:bg-emerald-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Dalam Proses</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->whereIn('status', ['IN_STORAGE', 'PENDING_SHIPMENT_CONFIRMATION']);
                                    })->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/40 dark:to-emerald-900/20 rounded-xl">
                            <x-heroicon-o-arrow-path class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang sedang dalam proses penanganan</p>
                </div>
            </div>
        </div>
    </div>
</div>