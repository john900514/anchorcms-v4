<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailHasBeenVerified extends ShouldBeStored
{
    public $id, $date;

    public function __construct(string $id, string $date)
    {
        $this->id = $id;
        $this->date = $date;
    }
}
