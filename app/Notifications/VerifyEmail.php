<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('Verify Your Email Address - Tech Store'))
            ->greeting(Lang::get('Hello, ' . $notifiable->first_name . '!'))
            ->line(Lang::get('Thank you for registering with Tech Store. We\'re excited to have you as part of our community.'))
            ->line(Lang::get('Please click the button below to verify your email address and activate your account.'))
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('Once verified, you\'ll gain full access to our premium tech products and exclusive offers.'))
            ->line(Lang::get('If you did not create an account, no further action is required.'))
            ->salutation(Lang::get('Regards,<br>The Tech Store Team'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}