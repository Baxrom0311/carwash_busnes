<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class SubscriptionSuspended extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [TelegramChannel::class];
    }

    public function toTelegram(object $notifiable)
    {
        $tenantName = $notifiable->name; // $notifiable bu Tenant modeli bo'ladi

        return TelegramMessage::create()
            ->content("Hurmatli *{$tenantName}*! Sizning platformaga obunangiz to'lov amalga oshirilmaganligi sababli vaqtincha to'xtatildi. Iltimos, to'lovni amalga oshiring.")
            ->options(['parse_mode' => 'Markdown']);
    }
}
