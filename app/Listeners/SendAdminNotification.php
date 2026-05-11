<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\AdminNewUser;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotification
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
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $user = $event->user;
        $admins = User::admins()->get();
        logger("sending admin email");
        foreach($admins as $admin){
            $admin->notify(new AdminNewUser($user));
        }

    }
}
