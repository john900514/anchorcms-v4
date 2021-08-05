<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserAssignCapeAndBayDepartment extends ShouldBeStored
{
    public $id, $dept, $date, $modifier;

    public function __construct(string $id, string $dept, string $date, string $modifier)
    {
        $this->id = $id;
        $this->dept = $dept;
        $this->date = $date;
        $this->modifier = $modifier;
    }
}
