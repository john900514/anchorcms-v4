<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAssignedClientRole extends ShouldBeStored
{
    public function __construct()
    {
    }
}
