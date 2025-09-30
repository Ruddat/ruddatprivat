<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(public Feedback $feedback) {}

    public function via($notifiable): array
    {
        $channels = [];

        if (\App\Helpers\SettingsHelper::get('notifications.feedback_mail', 0)) {
            $channels[] = 'mail';
        }

        if (\App\Helpers\SettingsHelper::get('notifications.feedback_inapp', 1)) {
            $channels[] = 'database';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Neues Kunden-Feedback')
            ->line("Titel: {$this->feedback->title}")
            ->line("Kategorie: {$this->feedback->category}")
            ->line(substr($this->feedback->message, 0, 120).'...')
            ->action('Zum Feedback', url('/admin/feedback'));
    }

    public function toArray($notifiable): array
    {
        return [
            'id'       => $this->feedback->id,
            'title'    => $this->feedback->title,
            'category' => $this->feedback->category,
            'status'   => $this->feedback->status,
        ];
    }
}