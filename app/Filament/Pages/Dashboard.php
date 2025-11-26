<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\Stats\GlobalStatsWidget;
use App\Filament\Widgets\Charts\ItemsByCategoryChart;
use App\Filament\Widgets\PendingVerificationWidget;
// use App\Filament\Widgets\OperatorActionsWidget;
// use App\Filament\Widgets\Stats\OperatorStatsWidget;
use App\Filament\Widgets\LatestItemsWidget;
use App\Filament\Widgets\InStorageItemsWidget;
use App\Filament\Widgets\StorageManagementWidget;
use App\Filament\Widgets\DisposalWidget;
use App\Filament\Widgets\ShipmentConfirmationWidget;
use App\Filament\Widgets\PendingPickupWidget;
use App\Filament\Widgets\Charts\ConfiscatedItemsTrendChart;
use App\Filament\Widgets\Charts\FinalDispositionChart;
use App\Filament\Widgets\Charts\AirlinesChart;


class Dashboard extends BaseDashboard
{
    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        // Jika user adalah operator, tampilkan header kustom kita
        if (auth()->user()->role === 'operator_avsec') {
            return view('filament.pages.partials.operator-dashboard-header');
        }

        // Untuk peran lain, tidak ada header (atau bisa dibuatkan juga)
        return null;
    }

     public function getWidgets(): array
    {
        if (auth()->user()->role === 'operator_avsec') {
            return [
                LatestItemsWidget::class,
            ];
        }

            // Untuk semua peran lainnya (Admin, Dept Head, dll), tampilkan widget ini.
            return [
                GlobalStatsWidget::class,
                PendingVerificationWidget::class,
                PendingPickupWidget::class,
                ConfiscatedItemsTrendChart::class,
                ItemsByCategoryChart::class,
                AirlinesChart::class,
                FinalDispositionChart::class,
                StorageManagementWidget::class,
                InStorageItemsWidget::class,
                ShipmentConfirmationWidget::class,
                DisposalWidget::class,
            ];
        }

    public function getColumns(): int | array
    {
        return 2;
    }
}