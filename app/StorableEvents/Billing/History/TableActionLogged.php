<?php

namespace App\StorableEvents\Billing\History;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TableActionLogged extends ShouldBeStored
{
    public string $report, $time, $action;

    public function __construct(string $report, string $time, string $action)
    {
        $this->report = $report;
        $this->time = $time;
        $this->action = $action;
    }
}
