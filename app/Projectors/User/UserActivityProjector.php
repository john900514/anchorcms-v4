<?php

namespace App\Projectors\User;

use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\User\History\EmailHasBeenVerified;
use App\StorableEvents\User\History\PasswordUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserActivityProjector extends Projector
{
    public function onEmailHasBeenVerified(EmailHasBeenVerified $event)
    {
        $modifier = User::find($event->id);
        $modifier->email_verified_at = $event->date;
        $modifier->save();

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->id,
            'detail' => 'verified',
            'value' => $event->date,
        ]);

        $detail->misc = [
            'date' => $event->date,
            'message' => 'User completed registration on ' . $event->date
        ];

        $detail->active = true;
        $detail->save();
    }

    public function onPasswordUpdated(PasswordUpdated $event)
    {
        $modifier = User::find($event->id);
        $modifier->password = $event->value;
        $modifier->save();

        $detail = UserDetails::firstOrCreate([
            'user_id' => $event->id,
            'detail' => 'password',
            'value' => $event->value,
        ]);

        $detail->misc = [
            'date' => $event->date,
            'message' => 'Password was updated on ' . $event->date . ' by ' . $modifier->name
        ];

        $detail->active = true;
        $detail->save();

        $records_to_deactivate = UserDetails::whereUserId($event->id)
            ->whereDetail('password')
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
