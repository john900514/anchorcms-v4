<?php

namespace App\Reactors\User;

use App\Mail\User\NewAdmin;
use App\Models\User;
use App\StorableEvents\User\History\HistoryHasBeenEstablished;
use App\StorableEvents\User\History\UserAssignedCapeAndBayRole;
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
}
