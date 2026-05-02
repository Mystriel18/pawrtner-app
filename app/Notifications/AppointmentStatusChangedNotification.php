<?php

namespace App\Notifications;

use App\Filament\Client\Resources\Appointments\AppointmentResource as ClientAppointmentResource;
use App\Filament\Resources\Appointments\AppointmentResource as AdminAppointmentResource;
use App\Filament\Vet\Resources\Appointments\AppointmentResource as VetAppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Appointment $appointment,
        private readonly string $status,
        private readonly ?string $note,
        private readonly ?User $actor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabel = Appointment::getStatusOptions()[$this->status] ?? ucfirst($this->status);
        $petName = $this->appointment->pet?->name ?? 'your pet';
        $actorName = $this->actor?->name ?? 'Clinic staff';

        return [
            'format' => 'filament',
            'duration' => 'persistent',
            'title' => 'Appointment ' . $statusLabel,
            'body' => sprintf('%s changed %s\'s appointment to %s.', $actorName, $petName, $statusLabel),
            'appointment_id' => $this->appointment->id,
            'pet_id' => $this->appointment->pet_id,
            'status' => $this->status,
            'status_label' => $statusLabel,
            'note' => $this->note,
            'scheduled_at' => optional($this->appointment->scheduled_at)?->toDateTimeString(),
            'url' => $this->resolveTargetUrl($notifiable),
        ];
    }

    private function resolveTargetUrl(object $notifiable): string
    {
        if (! $notifiable instanceof User) {
            return url('/');
        }

        if ($notifiable->hasRole('admin')) {
            return AdminAppointmentResource::getUrl(panel: 'admin');
        }

        if ($notifiable->hasRole('vet')) {
            return VetAppointmentResource::getUrl(panel: 'vet');
        }

        return ClientAppointmentResource::getUrl(panel: 'client');
    }
}
