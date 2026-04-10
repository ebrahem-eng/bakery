<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->message->id,
            'title' => 'New Contact Message',
            'message' => 'New message from ' . $this->message->name . ' (' . $this->message->email . ')',
            'url' => route('admin.contact-messages.show', $this->message->id),
        ];
    }
}
