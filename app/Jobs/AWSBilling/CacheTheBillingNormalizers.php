<?php

namespace App\Jobs\AWSBilling;

use App\Models\AWSBilling\AwsBilling;
use App\Models\Data\Reports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CacheTheBillingNormalizers implements ShouldQueue
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
    public function handle(Reports $report_model, AwsBilling $billing)
    {
        if(env('APP_ENV') == 'local')
        {
            echo "Flushing the cache right quick! \n";
            Cache::flush();
        }

        $report = Cache::remember($this->report_id, (60 * 10), function () use($report_model) {
            return $report_model->find($this->report_id);
        });

        $table_date = $report->misc['table_date'];
        // Use the $misc to update the table_name for the AWSBilling model
        echo "Changing the table name to awsbilling{$table_date}\n";
        $billing->changeTable($table_date);
        $newest_record = Cache::remember($this->report_id.'-newest', (60 * 10), function () use($billing) {
            return $billing->orderBy('identity_timeinterval', 'DESC')->first();
        });

        if(!is_null($newest_record))
        {
            $end_date = date('Y-m-d', strtotime($newest_record->lineitem_usagestartdate));
            $first_date = date('Y-m-', strtotime($newest_record->lineitem_usagestartdate)).'01';
            $current_date_being_worked_on = $first_date;

            $cache_key = env('APP_ENV').'-billing-product-names';
            //$billable_products = Cache::remember($cache_key, (60 * 60) * 2, function () use($billing) {
            $billable_products = Cache::remember($cache_key, (60 * 60), function () use($billing) {
                return $billing->getUniqueProductsAsArray();
            });

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
                        $cache_key = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-activity-'.$current_date_being_worked_on;
                        $current_days_current_products = Cache::get($cache_key, $billing->getAllRecordsByProductAndDate($product, $current_date_being_worked_on, true));
                        if(count($current_days_current_products) > 0)
                        {
                            $product_type_records = [];
                            $type_operation_records = [];

                            foreach ($current_days_current_products as $record)
                            {
                                $usage_type = $record['lineitem_usagetype'];
                                $operation = $record['lineitem_operation'];

                                if(!array_key_exists($usage_type, $product_type_records))
                                {
                                    $product_type_records[$usage_type] = [];
                                }

                                if(!array_key_exists($usage_type, $type_operation_records))
                                {
                                    $type_operation_records[$usage_type] = [];
                                }

                                if(!array_key_exists($operation, $type_operation_records[$usage_type]))
                                {
                                    $type_operation_records[$usage_type][$operation] = [];
                                }

                                $product_type_records[$usage_type][] = $record;
                                $type_operation_records[$usage_type][$operation][] = $record;
                            }

                            $save_cache_key_type = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-type-activity-'.$current_date_being_worked_on;
                            $save_cache_key_ops  = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-operation-activity-'.$current_date_being_worked_on;

                            Cache::put($save_cache_key_type,
                                json_encode($product_type_records),
                                (60 * 60) * 2);

                            Cache::put($save_cache_key_ops,
                                json_encode($type_operation_records),
                                (60 * 60) * 2);
                        }
                    }

                    $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
                }

                if($current_date_being_worked_on == $end_date)
                {
                    echo "Final Date Being Worked On - {$current_date_being_worked_on} \n";
                    foreach ($billable_products as $product)
                    {
                        echo "Product/Date Being Worked On - {$product} {$current_date_being_worked_on} \n";
                        $cache_key = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-activity-'.$current_date_being_worked_on;
                        $current_days_current_products = Cache::get($cache_key, $billing->getAllRecordsByProductAndDate($product, $current_date_being_worked_on));
                        if(count($current_days_current_products) > 0)
                        {
                            $product_type_records = [];
                            $type_operation_records = [];

                            foreach ($current_days_current_products as $record)
                            {
                                $usage_type = $record['lineitem_usagetype'];
                                $operation = $record['lineitem_operation'];

                                if(!array_key_exists($usage_type, $product_type_records))
                                {
                                    $product_type_records[$usage_type] = [];
                                }

                                if(!array_key_exists($usage_type, $type_operation_records))
                                {
                                    $type_operation_records[$usage_type] = [];
                                }

                                if(!array_key_exists($operation, $type_operation_records[$usage_type]))
                                {
                                    $type_operation_records[$usage_type][$operation] = [];
                                }

                                $product_type_records[$usage_type][] = $record;
                                $type_operation_records[$usage_type][$operation][] = $record;
                            }

                            $save_cache_key_type = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-type-activity-'.$current_date_being_worked_on;
                            $save_cache_key_ops  = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-operation-activity-'.$current_date_being_worked_on;

                            Cache::remember($save_cache_key_type,
                                (60 * 15) , function() use($product_type_records) { return $product_type_records;});

                            Cache::remember($save_cache_key_ops,
                                (60 * 15), function() use($type_operation_records) { return $type_operation_records; });
                        }
                    }
                }
            }

            echo "Caching Completed! Doosiesss!!!! \n";
        }
        else
        {
            echo "No Data was Found. Wierd... Quitting! \n";
        }
    }
}
