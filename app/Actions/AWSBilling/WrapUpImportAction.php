<?php

namespace App\Actions\AWSBilling;

use App\Aggregates\Billing\AWSBillingReportAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class WrapUpImportAction
{
    use AsAction;

    public function handle(string $report_id)
    {
        AWSBillingReportAggregate::retrieve($report_id)
            ->wrapUpReport()
            ->persist();
    }
}
