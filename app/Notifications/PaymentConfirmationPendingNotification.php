<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationPendingNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Platba přijata - čeká na schválení - Sports Club')
            ->greeting('Dobrý den ' . $this->user->first_name . '!')
            ->line('Děkujeme za vaši platbu za členství v našem klubu.')
            ->line('Vaše platba byla úspěšně přijata a nyní čeká na schválení administrátorem.')
            ->line('**Variabilní symbol:** ' . $this->user->payment_reference)
            ->line('**Částka:** 500 Kč')
            ->line('Jakmile platbu ověříme, aktivujeme vaše členství a zašleme vám potvrzení.')
            ->line('Tento proces obvykle trvá 1-2 pracovní dny.')
            ->action('Sledovat stav členství', url('/membership'))
            ->line('Děkujeme za trpělivost!');
    }
}
