<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use Auth;

class ApproveFund extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $fund_id; 
    protected $amount; 
    protected $credit; 
    public function __construct($fund_id, $amount, $credit)
    {
        //
        $this->fund_id = $fund_id;
        $this->amount = $amount;
        $this->credit = $credit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $user = Auth::user();
        return [
            //
            'id' => $this->fund_id,
            'data'=>' Your fund request of '. number_format($this->amount, 2). ' has been approved your new credit balance is ₦'.number_format($this->credit, 2),
        ];
    }
}
