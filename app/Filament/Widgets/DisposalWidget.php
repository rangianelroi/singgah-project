<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;

class DisposalWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected string $view = 'filament.widgets.disposal-widget';
    public ?Collection $itemsForDisposal;

    #[On('item-processed')]
    public function mount(): void
    {
        // Ambil item yang statusnya IN_STORAGE dan sudah lebih dari 2 bulan
        // (Kita gunakan 2 bulan agar ada waktu 1 bulan untuk persiapan)
        $this->itemsForDisposal = ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
            $query->where('status', 'IN_STORAGE');
        })
        ->where('confiscation_date', '<=', now()->subMinute()) // Ubah menjadi 1 menit untuk tes
        ->get();
    }

    /**
     * Widget ini hanya bisa dilihat oleh Dept Head dan Admin
     */
    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin']);
    }

    /**
     * Aksi untuk memproses pemusnahan
     */
    public function processDisposalAction(): Action
    {
        return Action::make('processDisposal')
            ->label('Proses Pemusnahan')
            ->icon('heroicon-o-fire')
            ->color('danger')
            ->form([
                Select::make('disposal_method')
                    ->options([
                        'destroyed' => 'Dimusnahkan',
                        'handed_to_police' => 'Diserahkan ke Polisi',
                        'other' => 'Lainnya',
                    ])
                    ->required()->label('Metode Pemusnahan'),
                DatePicker::make('disposal_date')
                    ->required()->default(now())->label('Tanggal Pemusnahan'),
                Textarea::make('witnesses')
                    ->label('Saksi (jika ada, pisahkan dengan koma)')
                    ->helperText('Contoh: Budi, Anton, Candra'),
            ])
            ->action(function (array $data, Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) return;

                // Buat record pemusnahan baru
                $record->disposal()->create([
                    'disposal_method' => $data['disposal_method'],
                    'disposal_date' => $data['disposal_date'],
                    'witnesses' => $data['witnesses'],
                    'authorized_by_user_id' => auth()->id(),
                ]);

                // Update status log menjadi DISPOSED
                $record->statusLogs()->create([
                    'status' => 'DISPOSED',
                    'user_id' => auth()->id(),
                    'notes' => 'Barang telah diproses untuk pemusnahan dengan metode: ' . $data['disposal_method'],
                ]);

                Notification::make()->title('Barang Diproses!')->body('Status barang telah diubah menjadi DISPOSED.')->success()->send();
                $this->dispatch('item-processed');
            });
    }
}