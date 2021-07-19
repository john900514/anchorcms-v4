<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class HistoryHasBeenEstablished extends ShouldBeStored
{
    public $user, $created, $creator;

    public function __construct(string $user, string $created, string $creator)
    {
        $this->user = $user;
        $this->created = $created;
        $this->creator = $creator;
    }
}
