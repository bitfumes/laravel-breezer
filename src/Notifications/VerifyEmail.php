<?php

namespace Bitfumes\Breezer\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    private static $toMailCallback;

    /**
     * Get the notification's channels.
     *
     * @return array|string
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        return (new MailMessage())
            ->subject('Verify Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action(
                ('Verify Email Address'),
                $this->verificationUrl($notifiable)
            )
            ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $front_url   = app()['config']['breezer.front_url'];
        $verify_url  = app()['config']['breezer.verify_url'];
        $expires     = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->timestamp;
        $sign        = sha1($expires . $notifiable->getEmailForVerification());
        return "{$front_url}/{$verify_url}?user={$notifiable->getKey()}&expires=$expires&signature={$sign}";
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
