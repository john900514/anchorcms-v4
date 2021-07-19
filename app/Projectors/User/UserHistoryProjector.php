<?php

namespace App\Projectors\User;

use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\User\History\EmailUpdated;
use App\StorableEvents\User\History\HistoryHasBeenEstablished;
use App\StorableEvents\User\History\TimezoneUpdated;
use App\StorableEvents\User\History\UserAssignedCapeAndBayRole;
use App\StorableEvents\User\History\UsernameUpdated;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserHistoryProjector extends Projector
{
    public function onHistoryHasBeenEstablished(HistoryHasBeenEstablished $event)
    {
        $creator = User::find($event->creator);

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->user,
            'detail' => 'created',
            'value' => $event->created,
        ]);

        if (!is_null($creator)) {
            $detail->misc = [
                'date' => $event->created,
                'created_by_user_id' => $event->creator,
                'message' => 'Created on ' . $event->created . ' by ' . $creator->name
            ];
        }
        else
        {
            $detail->misc = [
                'date' => $event->created,
                'created_by_user_id' => null,
                'message' => 'Created on ' . $event->created . ' by the Matrix.'
            ];
        }

        $detail->save();
    }

    public function onUsernameUpdated(UsernameUpdated $event)
    {
        $modifier = User::find($event->modifier);

        $payload = [
            'user_id' => $event->id,
            'detail' => 'username',
            'value' => $event->value,
        ];

        $detail = UserDetails::create($payload);

        if($event->old == '')
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => 'Unknown',
                'message' => 'Username was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }
        else
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => $event->old,
                'message' => 'Timezone was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }

        $detail->active = true;
        $detail->save();

        $records_to_deactivate = UserDetails::whereUserId($event->id)
            ->whereDetail('username')
            ->whereActive(true)
            ->where('id', '!=', $detail->id)
            ->get();

        if(count($records_to_deactivate) > 0)
        {
            foreach($records_to_deactivate as $record)
            {
                $record->active = false;
                $record->save();
            }
        }
    }

    public function onEmailUpdated(EmailUpdated $event)
    {
        $modifier = User::find($event->modifier);

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->id,
            'detail' => 'email',
            'value' => $event->value,
        ]);

        if($event->old == '')
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => 'Unknown',
                'message' => 'Email Address was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }
        else
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => $event->old,
                'message' => 'Email Address was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }

        $detail->active = true;
        $detail->save();

        $records_to_deactivate = UserDetails::whereUserId($event->id)
            ->whereDetail('email')
            ->whereActive(true)
            ->where('id', '!=', $detail->id)
            ->get();

        if(count($records_to_deactivate) > 0)
        {
            foreach($records_to_deactivate as $record)
            {
                $record->active = false;
                $record->save();
            }
        }
    }

    public function onTimezoneUpdated(TimezoneUpdated $event)
    {
        $modifier = User::find($event->modifier);

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->id,
            'detail' => 'timezone',
            'value' => $event->value,
        ]);

        if($event->old == '')
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => 'Unknown',
                'message' => 'Timezone was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }
        else
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => $event->old,
                'message' => 'Timezone was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }

        $detail->active = true;
        $detail->save();

        $records_to_deactivate = UserDetails::whereUserId($event->id)
            ->whereDetail('timezone')
            ->whereActive(true)
            ->where('id', '!=', $detail->id)
            ->get();

        if(count($records_to_deactivate) > 0)
        {
            foreach($records_to_deactivate as $record)
            {
                $record->active = false;
                $record->save();
            }
        }
    }

    public function onUserAssignedCapeAndBayRole(UserAssignedCapeAndBayRole $event)
    {
        $user = User::find($event->id);
        Bouncer::assign($event->role)->to($user);

        $modifier = User::find($event->modifier);

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->id,
            'detail' => 'role',
            'value' => $event->role,
        ]);

        if(!is_null($modifier))
        {
            $detail->misc = [
                'date' => $event->date,
                'old_role' => 'Unknown',
                'message' => 'User Role was updated to '.$event->role.' on ' . $event->date . ' by ' . $modifier->name
            ];
        }
        else
        {
            $detail->misc = [
                'date' => $event->date,
                'old_value' => $event->old,
                'message' => 'Timezone was updated to '.$event->role.' on ' . $event->date . ' by the Matrix'
            ];
        }

        $detail->active = true;
        $detail->save();

        $records_to_deactivate = UserDetails::whereUserId($event->id)
            ->whereDetail('role')
            ->whereActive(true)
            ->where('id', '!=', $detail->id)
            ->get();

        if(count($records_to_deactivate) > 0)
        {
            foreach($records_to_deactivate as $record)
            {
                $record->active = false;
                $record->save();
            }
        }
    }
}
