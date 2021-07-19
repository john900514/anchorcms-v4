<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAssignedCapeAndBayRole extends ShouldBeStored
{
    public $id, $role, $date, $modifier;

    public function __construct(string $id, string $role, string $date, string $modifier)
    {
        $this->id = $id;
        $this->role = $role;
        $this->date = $date;
        $this->modifier = $modifier;
    }
}
