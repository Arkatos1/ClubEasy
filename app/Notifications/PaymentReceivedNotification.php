<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public $user;
    public $amount;

    public function __construct(User $user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        // Returns array of channels: ['mail', 'database']
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nová platba čeká na schválení - Sports Club')
            ->greeting('Dobrý den!')
            ->line('Uživatel zaslal platbu za členství a čeká na vaše schválení.')
            ->line('**Uživatel:** ' . $this->user->name)
            ->line('**Email:** ' . $this->user->email)
            ->line('**Variabilní symbol:** ' . $this->user->payment_reference)
            ->line('**Částka:** ' . $this->amount . ' Kč')
            ->line('**Datum nahlášení:** ' . now()->format('d.m.Y H:i'))
            ->action('Zobrazit platby', url('/administration/payments'))
            ->line('Prosím ověřte platbu v bankovním systému a potvrďte členství.')
            ->line('Děkujeme za vaši práci!');
    }
}
