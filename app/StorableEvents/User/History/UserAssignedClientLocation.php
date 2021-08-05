<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAssignedClientLocation extends ShouldBeStored
{
    public $id, $location, $date, $modifier;

    public function __construct(string $id, string $location, string $date, string $modifier)
    {
        $this->id = $id;
        $this->location = $location;
        $this->date = $date;
        $this->modifier = $modifier;
    }
}
