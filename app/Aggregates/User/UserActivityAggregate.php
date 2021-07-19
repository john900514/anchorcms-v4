<?php

namespace App\Aggregates\User;

use App\Models\User;
use App\Exceptions\User\UserActivityException;
use App\StorableEvents\User\History\EmailHasBeenVerified;
use App\StorableEvents\User\History\EmailUpdated;
use App\StorableEvents\User\History\PasswordUpdated;
use App\StorableEvents\User\History\TimezoneUpdated;
use App\StorableEvents\User\History\UsernameUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use App\StorableEvents\User\History\HistoryHasBeenEstablished;
use \App\StorableEvents\User\History\UserAssignedCapeAndBayRole;

class UserActivityAggregate extends AggregateRoot
{
    protected $date_created, $created_by_user_id;
    protected $history = [];
    protected $verified = false;

    protected static bool $allowConcurrency = true;

    public function applyHistoryHasBeenEstablished(HistoryHasBeenEstablished $event)
    {
        $this->date_created = $event->created;
        $this->created_by_user_id = $event->creator;

        $creator = $this->getCreatorUser();

        if (!is_null($creator)) {

            $this->history[] = [
                'date' => $this->date_created,
                'event' => 'created',
                'message' => 'Created on ' . $this->date_created . ' by ' . $creator->name
            ];
        }
        else
        {
            $this->history[] = [
                'date' => $this->date_created,
                'event' => 'created',
                'message' => 'Created on ' . $this->date_created . ' by the Matrix.'
            ];
        }

    }

    public function applyUsernameUpdated(UsernameUpdated $event)
    {
        $modifier = User::find($event->modifier);
        $history = [
            'date' => $event->date,
            'event' => 'username',
        ];

        if($event->old == '')
        {
            $history['message'] = 'Username was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }
        else
        {
            $history['message'] = 'Username was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }

        $this->history[] = $history;
    }

    public function applyEmailUpdated(EmailUpdated $event)
    {
        $modifier = User::find($event->modifier);
        $history = [
            'date' => $event->date,
            'event' => 'email',
        ];

        if($event->old == '')
        {
            $history['message'] = 'Email was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }
        else
        {
            $history['message'] = 'Email was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }

        $this->history[] = $history;
    }

    public function applyTimezoneUpdated(TimezoneUpdated $event)
    {
        $modifier = User::find($event->modifier);
        $history = [
            'date' => $event->date,
            'event' => 'timezone',
            'old' => $event->old,
            'new' => $event->value
        ];

        if($event->old == '')
        {
            $history['message'] = 'Timezone was updated to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }
        else
        {
            $history['message'] = 'Timezone was updated from '.$event->old.' to '.$event->value.' on ' . $event->date . ' by ' . $modifier->name;
        }

        $this->history[] = $history;
    }

    public function applyPasswordUpdated(PasswordUpdated $event)
    {
        $history = [
            'date' => $event->date,
            'event' => 'password',
        ];

        $history['message'] = 'User updated their password on ' . $event->date;

        $this->history[] = $history;
    }

    public function applyEmailHasBeenVerified(EmailHasBeenVerified $event)
    {
        $history = [
            'date' => $event->date,
            'event' => 'verified',
        ];

        $history['message'] = 'User completed registration on ' . $event->date;

        $this->history[] = $history;
    }

    /**
     * @param array $user
     * @param string $creator_id
     * @return $this
     * @throws UserActivityException
     */
    public function createUserHistory(array $user, string $creator_id)
    {
        if(is_null($this->date_created))
        {
            $this->recordThat(new HistoryHasBeenEstablished($user['id'], $user['created_at'], $creator_id));
        }
        else
        {
            throw UserActivityException::userAlreadyCreated();
        }

        return $this;
    }

    public function setUsername(string $name, string $old, string $date, string $modifier_id)
    {
        $this->recordThat(new UsernameUpdated($this->uuid(), $name, $old, $date,$modifier_id));
        return $this;
    }

    public function setEmail(string $email, string $old, string $date, string $modifier_id)
    {
        $this->recordThat(new EmailUpdated($this->uuid(), $email, $old, $date, $modifier_id));
        return $this;
    }

    public function setTimezone(string $tz, string $old, string $date, string $modifier_id)
    {
        $this->recordThat(new TimezoneUpdated($this->uuid(), $tz, $old, $date, $modifier_id));
        return $this;
    }

    public function setAdminRole(string $id, string $role, string $date, string $modifier_id)
    {
        $this->recordThat(new UserAssignedCapeAndBayRole($id, $role,$date, $modifier_id));
        return $this;
    }

    /**
     * @param string $raw_pw
     * @param string $date
     * @param string $modifier_id
     * @return $this
     * @throws UserActivityException
     */
    public function setPassword(string $raw_pw, string $date, string $modifier_id)
    {
        if($modifier_id != $this->uuid())
        {
            throw UserActivityException::accountOwnerOnlyAbility('password');
        }

        $hashed_pw = bcrypt($raw_pw);
        $this->recordThat(new PasswordUpdated($this->uuid(), $hashed_pw, $date, $modifier_id));

        return $this;
    }

    /**
     * @param string $date
     * @return $this
     * @throws UserActivityException
     */
    public function setUserVerified(string $date)
    {
        if($this->userHasBeenVerified())
        {
            throw UserActivityException::userAlreadyVerified();
        }

        $this->recordThat(new EmailHasBeenVerified($this->uuid(), $date));
        return $this;
    }

    public function historyBeenEstablished() : bool
    {
        return (!is_null($this->date_created));
    }

    public function userHasBeenVerified() : bool
    {
        return $this->verified;
    }

    public function getCreatorUser(bool $id_only = false)
    {
        $results = false;
        if($this->historyBeenEstablished())
        {
            if($id_only)
            {
                $results = $this->date_created;
            }
            else
            {
                $results = User::find($this->created_by_user_id);
            }
        }

        return $results;
    }

    public function getLastUpdatedTimeZone()
    {
        $results = '';
        $history = collect($this->history);
        $tx_history = $history->where('event', '=', 'timezone')->sortByDesc('date')->first();

        if(!is_null($tx_history))
        {
            // @todo - finish this when there's data to query
            $results = $tx_history['new'];
        }

        return $results;
    }
}
