<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RejectRegistration extends Notification
{
    use Queueable;

    protected $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your account has been rejected.')
            ->line('Reason for rejection: ' . $this->reason)
            ->line('If you have any questions, please contact our support team.')
            ->action('Contact Support', url('/contact'))
            ->line('Thank you for your understanding.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'User Account Rejected',
            'body' => "Your account has been rejected. Reason: {$this->reason}",
            'type' => 'danger',
        ];
    }
}
