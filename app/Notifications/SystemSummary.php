<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class SystemSummary extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->content(trim("
---------------------------------------
# Chia Price
XCH: \${$this->data['xchPrice']}

# Wallet Summary
Wallet balance: \${$this->data['walletBalanceUsd']}
Wallet balance: {$this->data['walletBalance']} xch

# Plots
Plot count: {$this->data['plotCount']}
Plot size: {$this->data['plotSize']}
Avg. plot time: {$this->data['avgTotalTime']}s
Avg. plot time: {$this->data['avgTotalTimeMin']} min

# chia-1 Sensors
CPU: {$this->data['chia1Sensors']['cpu']}°C
NVME: {$this->data['chia1Sensors']['nvme']}°C
        "));
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
}
