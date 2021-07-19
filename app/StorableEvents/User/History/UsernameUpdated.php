<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UsernameUpdated extends ShouldBeStored
{
    public $id, $old, $value, $date, $modifier;
    public function __construct(string $id, string $value, string $old, string $date, string $modifier)
    {
        $this->id = $id;
        $this->old = $old;
        $this->date = $date;
        $this->value = $value;
        $this->modifier = $modifier;
    }
}
