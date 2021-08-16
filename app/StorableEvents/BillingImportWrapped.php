<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class BillingImportWrapped extends ShouldBeStored
{
    public $report;
    public function __construct(string $report)
    {
        $this->report = $report;
    }
}
