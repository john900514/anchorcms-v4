<?php

namespace App\Reactors\User;

use App\Mail\User\NewAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\StorableEvents\User\History\HistoryHasBeenEstablished;
use App\StorableEvents\User\History\UserAssignedCapeAndBayRole;
use App\StorableEvents\User\History\VaultTokenUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class UserHistoryReactor extends Reactor implements ShouldQueue
{
    public function onUserAssignedCapeAndBayRole(UserAssignedCapeAndBayRole $event)
    {
        $user = User::find($event->id);
        Mail::to($user->email)->send(new NewAdmin($event->id, $event->role, $event->date, $event->modifier));
    }

    public function onVaultTokenUpdated(VaultTokenUpdated $event)
    {
        // Here we will use bouncer to assign the user's Role
        $user = User::find($event->id);

        if(!is_null($event->token))
        {
            Log::info($event->id.' - allowing to view secrets vault');
            Bouncer::allow($user)->to('view-secrets-vault');
        }
        else
        {
            Log::info($event->id.' - disallowing to view secrets vault');
            Bouncer::disallow($user)->to('view-secrets-vault');
        }
    }
}
