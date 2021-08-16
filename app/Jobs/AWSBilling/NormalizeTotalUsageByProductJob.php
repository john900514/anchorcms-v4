<?php

namespace App\Jobs\AWSBilling;

use App\Models\AWSBilling\AwsBilling;
use App\Models\AWSBilling\ProductUsageMonthly;
use App\Models\Data\Reports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class NormalizeTotalUsageByProductJob implements ShouldQueue
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
    public function handle(Reports $report_model, AwsBilling $billing, ProductUsageMonthly $usage_model)
    {
        $report = Cache::get($this->report_id, $report_model->find($this->report_id));
        $table_date = $report->misc['table_date'];
        $billing->changeTable($table_date);

        echo "Getting the Products AWS Nickel and Dime's us over...\n";
        $cache_key = env('APP_ENV').'-billing-product-names';
        $billable_products = Cache::get($cache_key, $billing->getUniqueProductsAsArray());
        $amt_products = count($billable_products);
        echo "Done! Found {$amt_products} products! \n";

        if($amt_products > 0)
        {
            foreach ($billable_products as $product)
            {
                echo "Product Being Worked On - {$product} \n";
                $current_products = $billing->getAllRecordsByProduct($product, true);

                if(count($current_products) > 0)
                {
                    echo "Found ".count($current_products)." records to add up! \n";
                    $usage_cost = 0;
                    foreach ($current_products as $record)
                    {
                        $usage_cost += $record['pricing_publicondemandcost'];
                    }

                    $payload = [
                        'date' => date('Y-m', strtotime($report->misc['table_date'])),
                        'product' => $product
                    ];
                    $model = $usage_model->firstOrCreate($payload);
                    $model->usage_cost = number_format($usage_cost, 2, '.', '');
                    $model->save();
                }

                //$cache_key = env('APP_ENV').'-'.$this->report_id.'-total_product_activity-'.$product;
                //$current_products = Cache::get($cache_key, $billing->getAllRecordsByProduct($product));


            }
        }
    }
}
