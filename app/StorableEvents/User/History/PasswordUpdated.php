<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PasswordUpdated extends ShouldBeStored
{
    public $id, $value, $date, $modifier;
    public function __construct(string $id, string $value, string $date, string $modifier)
    {
        $this->id = $id;
        $this->date = $date;
        $this->value = $value;
        $this->modifier = $modifier;
    }
}
