<div class="space-y-6 py-4">
    {{-- Data Penjemput --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-4">Data Penjemput</h3>
        
        <div class="space-y-3">
            <div>
                <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Nama</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $pickup->pickup_by_name }}</p>
            </div>
            
            <div>
                <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">No. Identitas</p>
                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $pickup->pickup_by_identity_number }}</p>
            </div>
            
            <div>
                <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Hubungan dengan Penumpang</p>
                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $pickup->relationship_to_passenger }}</p>
            </div>

            @if($pickup->pickup_timestamp)
            <div>
                <p class="text-xs font-medium text-blue-800 dark:text-blue-400 uppercase tracking-wider">Waktu Pengambilan</p>
                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $pickup->pickup_timestamp->format('d M Y - H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Foto Penerima --}}
    @if($pickup->photo_of_recipient_path)
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Foto Penerima</h3>
        <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <img 
                src="{{ asset('storage/' . $pickup->photo_of_recipient_path) }}" 
                alt="Foto Penerima" 
                class="w-full h-auto max-h-96 object-contain bg-gray-100 dark:bg-gray-900"
            />
        </div>
    </div>
    @endif

    {{-- Foto Identitas --}}
    @if($pickup->photo_of_identity_path)
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Foto Identitas</h3>
        <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <img 
                src="{{ asset('storage/' . $pickup->photo_of_identity_path) }}" 
                alt="Foto Identitas" 
                class="w-full h-auto max-h-96 object-contain bg-gray-100 dark:bg-gray-900"
            />
        </div>
    </div>
    @endif

    @if(!$pickup->photo_of_recipient_path && !$pickup->photo_of_identity_path)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
        <p class="text-sm text-yellow-800 dark:text-yellow-300">
            <strong>Catatan:</strong> Belum ada foto dokumentasi untuk kerabat ini.
        </p>
    </div>
    @endif
</div>
