<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Membership Activated - Sports Club'))
            ->greeting(__('Hello :name!', ['name' => $notifiable->first_name]))
            ->line(__('Your membership payment has been verified and your account has been activated!'))
            ->line(__('**Membership Type:** Premium Member'))
            ->line(__('**Activation Date:** ') . now()->format('d.m.Y'))
            ->line(__('You now have access to all member benefits:'))
            ->line('✅ ' . __('Tournament participation'))
            ->line('✅ ' . __('Event priority registration'))
            ->line('✅ ' . __('Advanced training resources'))
            ->line('✅ ' . __('Member-only events'))
            ->action(__('Access Member Features'), url('/membership'))
            ->line(__('Thank you for joining our club!'));
    }
}
