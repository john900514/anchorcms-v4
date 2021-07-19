<?php

namespace App\Models\EventSourcing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class StoredEvent extends EloquentStoredEvent
{
    protected $connection = 'event-sourcing';
}
