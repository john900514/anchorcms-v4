<?php

namespace App\Projectors\Billing;

use App\Actions\AWSBilling\WrapUpImportAction;
use App\Jobs\AWSBilling\CacheTheBillingImport;
use App\Jobs\AWSBilling\CacheTheBillingNormalizers;
use App\Jobs\AWSBilling\NormalizeTotalUsageByDateJob;
use App\Jobs\AWSBilling\NormalizeTotalUsageByProductAndDateJob;
use App\Jobs\AWSBilling\NormalizeTotalUsageByProductJob;
use App\Jobs\AWSBilling\NormalizeTotalUsageByProductTypeAndDateJob;
use App\Jobs\AWSBilling\NormalizeTotalUsageByProductTypeOperationAndDateJob;
use App\Models\Data\Reports;
use App\StorableEvents\Billing\History\TableActionLogged;
use App\StorableEvents\BillingImportWrapped;
use Illuminate\Support\Facades\Bus;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AWSBillingHistoryProjector extends Projector
{
    public function onTableActionLogged(TableActionLogged $event)
    {
        switch($event->action)
        {
            case 'imported':
                $q_chain = [
                    //new CacheTheBillingImport($event->report), // cache the data
                    new NormalizeTotalUsageByDateJob($event->report),
                    new NormalizeTotalUsageByProductJob($event->report),
                    new NormalizeTotalUsageByProductAndDateJob($event->report),
                    new CacheTheBillingNormalizers($event->report), // cache the normalization data
                    new NormalizeTotalUsageByProductTypeAndDateJob($event->report), // cache the normalization data
                    new NormalizeTotalUsageByProductTypeOperationAndDateJob($event->report), // cache the normalization data
                    WrapUpImportAction::makeJob($event->report)
                ];

                Bus::chain($q_chain)
                    ->onQueue(env('APP_ENV').'-anchor-cache')
                    ->delay(now()->addSeconds(5))
                    ->dispatch();

                break;
        }
    }

    public function onBillingImportWrapped(BillingImportWrapped $event)
    {
        $report_model = Reports::find($event->report);
        $time = date('Y-m-d H:i:s');
        $report_model->time_completed = $time;
        $report_model->save();
    }
}
