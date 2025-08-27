<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Enum\NotificationType;
use App\Events\AfterNotificationCreation;
use App\Filament\Resources\NotificationResource;
use App\Models\NotificationCenter;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make(__('Send'))
                ->action(function (array $data) {
                    $data = $this->form->getState();
                    $notification = NotificationCenter::create([
                        'title' => $data['title'],
                        'body' => $data['body'],
                    ]);
                    $data['action'] = NotificationType::ADMIN->value;
                    $data['notification_center_id'] = $notification->id;
                    $this->sendNotification($data);
                    redirect(NotificationResource::getUrl('index'));
                }),
        ];
    }

    public function sendNotification(array $data): void
    {
        AfterNotificationCreation::dispatch($data);
        FilamentNotification::make()
            ->title('Success')
            ->body('Notification sent successfully!')
            ->success()
            ->send();
    }
}
