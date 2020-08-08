<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Picklist;

class PicklistProductMissing extends Notification
{
    use Queueable;

    private $picklist;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Picklist $picklist)
    {
        $this->picklist = $picklist;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Product Missing - ' . $this->picklist->order->order_number . ' - ' . $this->picklist->sku_ordered)
                    ->greeting('Product Missing')
                    ->line('Product ' . $this->picklist->sku_ordered . ', ' . $this->picklist->order->order_number . ', ' . $this->picklist->quantity_requested);
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
    public function shouldInterrupt($notifiable) {
        return !$this->picklist->productMissing();
    }
}
