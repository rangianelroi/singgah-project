<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\PendingVerificationWidget;
use App\Filament\Widgets\OperatorActionsWidget;
use App\Filament\Widgets\StorageManagementWidget;
use App\Filament\Widgets\InStorageItemsWidget;
use App\Filament\Widgets\DisposalWidget;
use App\Filament\Widgets\ShipmentConfirmationWidget;
use App\Filament\Widgets\Stats\GlobalStatsWidget;
use App\Filament\Widgets\Charts\ItemsByCategoryChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // 1. Atur Skema Warna
            ->colors([
                'primary' => '#1D3353',
                'gray'    => Color::Slate,
            ])

            // 2. Ganti Nama Brand
            ->brandName('SINGGAH')

            // 3. Atur Logo (jika Anda punya file logo)
            // ->brandLogo(asset('images/logo.svg')) 
            
            // 4. Atur Favicon (ikon di tab browser)
            // ->favicon(asset('images/favicon.png'))

            // 5. Jadikan Mode Gelap sebagai Default
            ->darkMode(true)

            // 6. Atur Font (opsional, jika ingin mengganti)
            ->font('Poppins')

            ->viteTheme('resources/css/filament/admin/theme.css')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // ItemsByCategoryChart::class,
                AccountWidget::class,
                // FilamentInfoWidget::class,
                // PendingVerificationWidget::class,
                // OperatorActionsWidget::class,
                // StorageManagementWidget::class,  
                // InStorageItemsWidget::class,
                // DisposalWidget::class,
                // GlobalStatsWidget::class,
                // ShipmentConfirmationWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
