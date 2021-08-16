<?php

namespace App\Jobs\AWSBilling;

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
                    echo "Product/Date Being Worked On - {$product} {$current_date_being_worked_on} \n";
                    $save_cache_key_ops  = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-operation-activity-'.$current_date_being_worked_on;

                    $product_type_records = Cache::get($save_cache_key_ops);

                    if(count($product_type_records) > 0)
                    {
                        echo "Found ".count($product_type_records)." product types containing operations! \n";
                        foreach ($product_type_records as $product_type => $type_operation_records)
                        {
                            echo "Product Type Being Worked On - {$product_type} \n";
                            foreach ($type_operation_records as $operation_type => $records)
                            {
                                echo "{ $product_type } Operation Being Worked On - {$operation_type} \n";
                                echo "Found ".count($records)." records to add up! \n";

                                $usage_cost = 0;
                                $usage_amt = 0;
                                foreach ($records as $record)
                                {
                                    $usage_cost += $record['pricing_publicondemandcost'];
                                    $usage_amt  += $record['lineitem_usageamount'];
                                }

                                $payload = [
                                    'date' => $current_date_being_worked_on,
                                    'product' => $product,
                                    'usage_type' => $product_type,
                                    'operation_performed' => $operation_type
                                ];
                                $rate = empty($records[0]['pricing_publicondemandrate']) ? 0.00 : $records[0]['pricing_publicondemandrate'];
                                $model = $usage_model->firstOrCreate($payload);
                                $model->desc = $records[0]['lineitem_lineitemdescription'];
                                $model->pricing_rate = number_format($rate, 2, '.');
                                $model->units_used = number_format($usage_amt, 2, '.');
                                $model->usage_cost   = number_format($usage_cost, 2, '.');
                                $model->desc = $records[0]['lineitem_lineitemdescription'];
                                $model->save();
                            }
                        }

                    }
                }
                $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
            }
        }
        else
        {
            echo "No Data was Found. Wierd... Quitting! \n";
        }

    }
}
