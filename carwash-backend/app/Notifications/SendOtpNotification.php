<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class SendOtpNotification extends Notification
{
    use Queueable;

    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via(object $notifiable): array
    {
        // Bu xabarni qaysi kanallar orqali yuborish kerakligini aytamiz.
        return [TelegramChannel::class];
    }

    public function toTelegram(object $notifiable)
    {
        // Telegram orqali yuboriladigan xabar matnini shakllantiramiz.
        return TelegramMessage::create()
            ->content("Sizning tasdiqlash kodingiz: *{$this->code}*")
            ->options(['parse_mode' => 'Markdown']);
    }
}
