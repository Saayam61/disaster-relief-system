<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FloodAlertNotification extends Notification
{
    use Queueable;

    public $message;
    public $severity;

    public function __construct($message, $severity)
    {
        $this->message = $message;
        $this->severity = $severity;
    }

    public function via($notifiable)
    {
        return ['database']; // You can add 'mail', 'sms', 'broadcast' etc.
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'severity' => $this->severity,
        ];
    }
}