<?php
namespace App\Filament\Widgets;

use App\Filament\Resources\ConfiscatedItemResource;
use App\Models\ConfiscatedItem;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;

class PendingVerificationWidget extends Widget
{
    use InteractsWithActions; // <-- Tambahkan ini
    use InteractsWithForms; // <-- Tambahkan ini
    
    protected string $view = 'filament.widgets.pending-verification-widget';

    public Collection $pendingItems;

    public function mount(): void
    {
        // Ambil item yang status terakhirnya adalah 'RECORDED'
        $this->pendingItems = ConfiscatedItem::whereHas('statusLogs', function ($query) {
            $query->where('status', 'RECORDED')->whereIn('id', function ($subQuery) {
                $subQuery->selectRaw('max(id) from item_status_logs group by item_id');
            });
        })->get();
    }
    
    // Atur hak akses: hanya tampil untuk peran yang bisa meng-update
    public static function canView(): bool
    {
        return auth()->user()->can('update', new ConfiscatedItem());
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            // ... (Seluruh kode Aksi "Setujui" dengan Wizard-nya Anda pindahkan ke sini)
            ;
    }
}