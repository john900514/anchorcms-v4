<?php

namespace App\Aggregates\Billing;

use App\StorableEvents\BillingImportWrapped;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use App\StorableEvents\Billing\History\TableActionLogged;

class AWSBillingReportAggregate extends AggregateRoot
{
    protected static bool $allowConcurrency = true;

    protected string $time_manipulated;
    protected string $time_imported;

    public function applyTableActionLogged(TableActionLogged $event)
    {
        switch($event->action)
        {
            case 'created':
            case 'truncated':
                $this->time_manipulated = $event->time;
                break;

            case 'imported':
                $this->time_imported = $event->time;
                break;
        }
    }

    public function logTableCreated(string $time)
    {
        $this->recordThat(new TableActionLogged($this->uuid(), $time, 'created'));
        return $this;
    }

    public function logTableTruncated(string $time)
    {
        $this->recordThat(new TableActionLogged($this->uuid(), $time, 'truncated'));
        return $this;
    }

    public function logTableImported(string $time)
    {
        $this->recordThat(new TableActionLogged($this->uuid(), $time, 'imported'));
        return $this;
    }

    public function wrapUpReport()
    {
        $this->recordThat(new BillingImportWrapped($this->uuid()));
        return $this;
    }
}
