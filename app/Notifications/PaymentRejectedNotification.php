<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification
{
    use Queueable;

    public $reason;

    public function __construct($reason = null)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject(__('Payment Issue - Sports Club'))
            ->greeting(__('Hello :name!', ['name' => $notifiable->first_name]))
            ->line(__('We encountered an issue with your membership payment.'));

        if ($this->reason) {
            $mailMessage->line(__('**Reason:** ') . $this->reason);
        } else {
            $mailMessage->line(__('The payment could not be verified in our system.'));
        }

        $mailMessage
            ->line(__('Please check the following:'))
            ->line('🔍 ' . __('Ensure the variable symbol was correct: ') . $notifiable->payment_reference)
            ->line('🔍 ' . __('Verify the amount was 500 CZK'))
            ->line('🔍 ' . __('Check your bank transaction status'))
            ->action(__('Try Again'), url('/membership'))
            ->line(__('If you believe this is an error, please contact us.'));

        return $mailMessage;
    }
}
