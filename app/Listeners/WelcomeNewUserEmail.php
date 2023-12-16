<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class WelcomeNewUserEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        if ($event->user instanceof MustVerifyEmail && $event->user->hasVerifiedEmail()) {
           //cooperative
            if($event->user->role == '2'){
                
                Mail::send('email.welcome-cooperative', $event->user, function($message) use ($event) {
                    $message->to($event->user->email);
                    $message->subject('Welcome Onboard');
                });
            }
            //merchant
            if($event->user->role == '3'){
                Mail::send('email.welcome-merchant', $event->user, function($message) use ($event) {
                    $message->to($event->user->email);
                    $message->subject('Welcome Onboard');
                });
            }
            //member
            if($event->user->role == '4'){
                Mail::send('email.welcome-member', $event->user, function($message) use ($event) {
                    $message->to($event->user->email);
                    $message->subject('Welcome Onboard');
                });
            }
            //fmcg
            if($event->user->role = 33){
                Mail::send('email.welcome-fmcg', $event->user, function($message) use ($event) {
                    $message->to($event->user->email);
                    $message->subject('Welcome Onboard');
                });
            }
        }
       
        
    }
}