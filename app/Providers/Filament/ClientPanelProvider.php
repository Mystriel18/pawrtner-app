<?php

namespace App\Providers\Filament;

use App\Filament\Client\Auth\Register;
use App\Filament\Client\Widgets\MyPetsWidget;
use App\Filament\Client\Widgets\UpcomingAppointmentsWidget;
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

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->viteTheme('resources/css/filament/client/theme.css')
            ->brandName('PAWrtner Client Portal')
            ->login()
            ->registration(Register::class)
            ->brandLogo(fn () => FilamentBrand::logo())
            ->brandLogoHeight('2.5rem')
            ->brandName('PAWrtner Client Portal')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => FilamentBrand::stylesheets(),
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => Color::hex('#f4a4bd'),
            ])
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\Filament\Client\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\Filament\Client\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->userMenuItems([
                'logout' => fn (Action $action): Action => $action
                    ->requiresConfirmation()
                    ->modalHeading('Log out?')
                    ->modalDescription('Are you sure you want to log out from PAWrtner Client Portal?')
                    ->modalSubmitActionLabel('Log out'),
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\Filament\Client\Widgets')
            ->widgets([
                MyPetsWidget::class,
                UpcomingAppointmentsWidget::class,
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
