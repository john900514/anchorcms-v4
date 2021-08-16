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
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class CacheTheBillingImport implements ShouldQueue
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
            $cq_chain = [];
            /* Caching Total Billing By Date */
            while($current_date_being_worked_on != $end_date)
            {
                echo "Date Being Worked On - {$current_date_being_worked_on} \n";
                $cache_key = env('APP_ENV').'-'.$this->report_id.'-total_activity-'.$current_date_being_worked_on;
                $cq_chain[] = new CacheTheTotalsByDate($cache_key, $current_date_being_worked_on, $table_date);

                $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
                //dd("Next Date - {$current_date_being_worked_on}");
            }

            if($current_date_being_worked_on == $end_date)
            {
                echo "Final Date = {$current_date_being_worked_on} \n";
                $cache_key = env('APP_ENV').'-'.$this->report_id.'-total_activity-'.$current_date_being_worked_on;

                $cq_chain[] = new CacheTheTotalsByDate($cache_key, $current_date_being_worked_on, $table_date);

                // Resetting the current date to the first date for the next bit of caching logic
                $current_date_being_worked_on = $first_date;
            }
            else
            {
                echo "Ooops did I miss something? = {$current_date_being_worked_on} \n";
            }

            if(count($cq_chain) > 0)
            {
                Bus::chain($cq_chain)
                    ->onQueue(env('APP_ENV').'-anchor-cache')
                    ->dispatch();
            }

            echo "Getting the Products AWS Nickel and Dime's us over...\n";
            $cache_key = env('APP_ENV').'-billing-product-names';
            //$billable_products = Cache::remember($cache_key, (60 * 60) * 2, function () use($billing) {
            $billable_products = Cache::remember($cache_key, (60 * 60), function () use($billing) {
                return $billing->getUniqueProductsAsArray();
            });

            $amt_products = count($billable_products);
            echo "Done! Found {$amt_products} products! \n";
            // Query and cache the data by product
            if($amt_products > 0)
            {
                $q_chain = [];
                foreach ($billable_products as $product)
                {
                    $cache_key = env('APP_ENV').'-'.$this->report_id.'-total_product_activity-'.$product;
                    echo "Product Being Worked On - {$product} \n";
                    $q_chain[] = new CacheTheTotalProducts($cache_key, $product, $table_date);
                }

                Bus::chain($q_chain)
                    ->onQueue(env('APP_ENV').'-anchor-cache')
                    ->dispatch();

                echo "Done caching Product Data! \n";
            }

            // query and cache the data by date AND product
            if($amt_products > 0)
            {
                echo "Working on Product/Date Caches Now\n";
                while($current_date_being_worked_on != $end_date)
                {
                    echo "Date Being Worked On - {$current_date_being_worked_on} \n";
                    foreach ($billable_products as $product)
                    {
                        echo "Product/Date Being Worked On - {$product} {$current_date_being_worked_on} \n";
                        $cache_key = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-activity-'.$current_date_being_worked_on;

                        Cache::put($cache_key,
                            $billing->getAllRecordsByProductAndDate($product, $current_date_being_worked_on),
                            (60 * 60) * 2);
                    }

                    $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
                }

                if($current_date_being_worked_on == $end_date)
                {
                    echo "Final Date = {$current_date_being_worked_on} \n";

                    foreach ($billable_products as $product)
                    {
                        echo "Product/Date Being Worked On - {$product} {$current_date_being_worked_on} \n";
                        $cache_key = env('APP_ENV').'-'.$this->report_id.'-total-'.$product.'-activity-'.$current_date_being_worked_on;

                        Cache::put($cache_key,
                            $billing->getAllRecordsByProductAndDate($product, $current_date_being_worked_on),
                            (60 * 60) * 2);
                    }

                    // Resetting the current date to the first date for the next bit of caching logic
                    $current_date_being_worked_on = $first_date;
                }
                else
                {
                    echo "Ooops Last One Too, did I miss something? = {$current_date_being_worked_on} \n";
                }
            }

            echo "Caching Completed! Bai!!!! \n";
        }

    }
}
