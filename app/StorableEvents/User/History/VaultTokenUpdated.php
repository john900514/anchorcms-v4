<?php

namespace App\StorableEvents\User\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class VaultTokenUpdated extends ShouldBeStored
{
    public $id, $token;

    public function __construct(string $id, string $token = null)
    {
        $this->id = $id;
        $this->token = $token;
    }
}
