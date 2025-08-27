<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class GlobalNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info('we are saving in database');

        return [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'action' => $this->data['action'],
            'action_id' => null,
            'notification_center_id' => $this->data['notification_center_id'],
        ];
    }

    /**
     * Get FCM message for push notifications
     */
    public function toFcm($notifiable): FcmMessage
    {
        $lang = $notifiable->getPreferedLanguage() ?? 'en';

        $title = $this->getTranslatedText('title', $lang);
        $body = $this->getTranslatedText('body', $lang);

        $message = fcmMessage($title, $body, $this->data['action']);

        Log::info('Sending FCM notification', [
            'title' => $title,
            'body' => $body,
            'language' => $lang,
        ]);

        return $message;
    }

    /**
     * Get translated text for a given field and language
     */
    private function getTranslatedText(string $field, string $lang): string
    {
        if (is_array($this->data[$field])) {
            return $this->data[$field][$lang] ?? $this->data[$field]['en'] ?? '';
        }

        return $this->data[$field] ?? '';
    }
}
