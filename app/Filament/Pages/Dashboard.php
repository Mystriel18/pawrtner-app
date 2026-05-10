<?php

namespace App\Filament\Pages;

use App\Services\ReminderDispatchService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('dispatchRemindersNow')
                ->label('Dispatch Reminders')
                ->icon('heroicon-o-bell-alert')
                ->color('primary')
                ->requiresConfirmation()
                ->form([
                    Select::make('type')
                        ->options([
                            'all' => 'All reminder types',
                            ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING => 'Appointment upcoming',
                            ReminderDispatchService::TYPE_APPOINTMENT_FOLLOW_UP => 'Appointment follow-up',
                            ReminderDispatchService::TYPE_VACCINATION_DUE => 'Vaccination due',
                        ])
                        ->default('all')
                        ->required(),
                    Toggle::make('dry_run')
                        ->label('Dry run only')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $summary = app(ReminderDispatchService::class)->dispatch(
                        type: (string) ($data['type'] ?? 'all'),
                        isDryRun: (bool) ($data['dry_run'] ?? false),
                    );

                    Notification::make()
                        ->title('Reminder dispatch completed')
                        ->body(sprintf(
                            'Type: %s | candidates=%d sent=%d skipped=%d failed=%d',
                            $summary['type'],
                            $summary['candidates'],
                            $summary['sent'],
                            $summary['skipped'],
                            $summary['failed'],
                        ))
                        ->success()
                        ->send();
                }),
            Action::make('openSignedReminderUrl')
                ->label('Open 15m Signed URL')
                ->icon('heroicon-o-link')
                ->color('gray')
                ->url(fn (): string => URL::temporarySignedRoute(
                    'internal.reminders.dispatch.signed',
                    now()->addMinutes(15),
                    ['type' => 'all'],
                ))
                ->openUrlInNewTab(),
        ];
    }
}
