<?php

namespace App\Models\EventSourcing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EventSourcing\Snapshots\EloquentSnapshot;

class Snapshot extends EloquentSnapshot
{
    protected $connection = 'event-sourcing';
}
