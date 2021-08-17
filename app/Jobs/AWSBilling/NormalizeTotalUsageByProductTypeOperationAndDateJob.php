<?php

namespace App\Jobs\AWSBilling;

use App\Actions\AWSBilling\BreakdownProductTypeOperationProcessing;
use App\Actions\AWSBilling\BreakdownProductTypeProcessing;
use App\Models\AWSBilling\AwsBilling;
use App\Models\AWSBilling\DailyProductUsageByType;
use App\Models\AWSBilling\DailyProductUsageByTypeOperation;
use App\Models\Data\Reports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class NormalizeTotalUsageByProductTypeOperationAndDateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $report_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Reports $report_model, AwsBilling $billing, DailyProductUsageByTypeOperation $usage_model)
    {
        $report = Cache::get($this->report_id, $report_model->find($this->report_id));
        $table_date = $report->misc['table_date'];
        $billing->changeTable($table_date);
        $newest_record = Cache::get($this->report_id.'-newest', $billing->orderBy('identity_timeinterval', 'DESC')->first());

        $end_date = date('Y-m-d', strtotime($newest_record->lineitem_usagestartdate));
        $first_date = date('Y-m-', strtotime($newest_record->lineitem_usagestartdate)).'01';
        $current_date_being_worked_on = $first_date;

        echo "Picking the Products AWS Nickel and Dime's us over with a fine tooth comb...\n";
        $cache_key = env('APP_ENV').'-billing-product-names';
        $billable_products = Cache::get($cache_key, $billing->getUniqueProductsAsArray());
        $amt_products = count($billable_products);

        if($amt_products > 0)
        {
            echo "Working on Caching Product/Date/Type Data Now\n";
            while($current_date_being_worked_on != $end_date)
            {
                echo "Date Being Worked On - {$current_date_being_worked_on} \n";
                foreach ($billable_products as $product)
                {
                    BreakdownProductTypeOperationProcessing::dispatch($this->report_id, $product, $current_date_being_worked_on)
                        ->onQueue(env('APP_ENV').'-anchor-cache');
                }
                $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
            }

            if($current_date_being_worked_on == $end_date)
            {
                echo "Final Date = {$current_date_being_worked_on} \n";
                foreach ($billable_products as $product)
                {
                    BreakdownProductTypeOperationProcessing::dispatch($this->report_id, $product, $current_date_being_worked_on)
                        ->onQueue(env('APP_ENV').'-anchor-cache');
                }
            }
        }
        else
        {
            echo "No Data was Found. Wierd... Quitting! \n";
        }

    }
}
