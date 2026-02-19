<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $notification) {}

    public function build()
    {
        return $this->subject($this->notification->title ?? 'New Notification')
            ->markdown('emails.notification');
    }
}
