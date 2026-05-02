<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientReminderNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly string $url,
        private readonly string $reminderType,
        private readonly array $meta = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'format' => 'filament',
            'duration' => 'persistent',
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'reminder_type' => $this->reminderType,
            'meta' => $this->meta,
        ];
    }
}
