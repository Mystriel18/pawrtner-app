<?php

namespace App\Providers\Filament;

use App\Filament\Vet\Widgets\VetAppointmentStatsOverview;
use App\Support\FilamentBrand;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class VetPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vet')
            ->path('vet')
            ->viteTheme('resources/css/filament/vet/theme.css')
            ->brandName('PAWrtner Vet Desk')
            ->login()
            ->brandLogo(fn () => FilamentBrand::logo())
            ->brandLogoHeight('2.5rem')
            ->brandName('PAWrtner Vet Desk')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => FilamentBrand::stylesheets(),
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Vet/Resources'), for: 'App\Filament\Vet\Resources')
            ->discoverPages(in: app_path('Filament/Vet/Pages'), for: 'App\Filament\Vet\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->userMenuItems([
                'logout' => fn (Action $action): Action => $action
                    ->extraAttributes([
                        'x-on:click' => 'if (!confirm(\'Are you sure you want to log out from PAWrtner Vet Desk?\')) { $event.preventDefault(); $event.stopImmediatePropagation(); }',
                    ]),
            ])
            ->discoverWidgets(in: app_path('Filament/Vet/Widgets'), for: 'App\Filament\Vet\Widgets')
            ->widgets([
                VetAppointmentStatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
