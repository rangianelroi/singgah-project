<div class="mb-8">
    {{-- Main Header Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-600 to-red-700 dark:from-amber-900/50 dark:to-amber-900/20 px-8 py-12 shadow-xl">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 dark:bg-amber-500/5 rounded-full -mr-48 -mt-48 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-amber-400/10 dark:bg-amber-400/5 rounded-full -ml-40 -mb-40 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-white/20 dark:bg-white/10 rounded-xl backdrop-blur-sm">
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p class="text-amber-100 mt-1">Kepala Departemen AVSEC | Dashboard Manajemen & Pengawasan</p>
                </div>
            </div>
            <p class="text-amber-50 text-base leading-relaxed max-w-2xl">
                Berikut adalah ringkasan lengkap status operasional keamanan bandara. Monitor semua aspek penyitaan barang dan pastikan semua prosedur keamanan berjalan sesuai standar.
            </p>
        </div>
    </div>

    {{-- Statistics Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        
        {{-- Card 1: Total Penyitaan Bulan Ini --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-600 to-amber-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-100 dark:bg-amber-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Penyitaan Bulan Ini</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-amber-100 to-amber-50 dark:from-amber-900/40 dark:to-amber-900/20 rounded-xl">
                            <x-heroicon-o-archive-box-arrow-down class="w-8 h-8 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total barang yang disita bulan ini</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Barang Terkirim --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-100 dark:bg-blue-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Barang Terkirim</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->where('status', 'SHIPPED');
                                    })->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20 rounded-xl">
                            <x-heroicon-o-truck class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang yang telah dikirim melalui ekspedisi</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Barang Dimusnahkan --}}
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600 to-red-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Decorative accent --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-100 dark:bg-red-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Barang Dimusnahkan</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ 
                                    \App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->where('status', 'DISPOSED');
                                    })->count() 
                                }}
                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/20 rounded-xl">
                            <x-heroicon-o-fire class="w-8 h-8 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang yang telah dimusnahkan/ditarik</p>
                </div>
            </div>
        </div>
    </div>
</div>
