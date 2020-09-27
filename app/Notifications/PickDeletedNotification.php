<?php

namespace App\Notifications;

use App\Models\Pick;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PickDeletedNotification extends Notification
{
    use Queueable;

    private $pick;

    /**
     * Create a new notification instance.
     *
     * @param Pick $pick
     */
    public function __construct(Pick $pick)
    {
        $this->pick = $pick;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pick deleted - ' . $this->pick->sku_ordered)
            ->greeting('This pick was deleted, some orders might not be picked')
            ->line('<a href="/products?search='.$this->pick->sku_ordered.'">SKU: ' . $this->pick->sku_ordered.'</a>')
            ->line('SKU: ' . $this->pick->sku_ordered)
            ->line('Name: ' . $this->pick->name_ordered)
            ->line('Quantity: ' . $this->pick->quantity_required);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Prevents the notification from being sent if the product is no longer missing.
     * This serves to catch cases when the pick is undid.
     */
    public function shouldInterrupt($notifiable)
    {
        return ! $this->pick->trashed();
    }
}
