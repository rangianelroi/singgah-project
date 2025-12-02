<x-filament-panels::page>
    <form wire:submit="exportPdf">
        
        {{-- Render Form Schema di sini --}}
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-m-printer" color="primary">
                Download PDF
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>