<?php

namespace App\Actions\AWSBilling;

use App\Models\AWSBilling\DailyProductUsageByType;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class BreakdownProductTypeProcessing
{
    use AsAction;

    public function handle(string $report_id, string $product, string $current_date_being_worked_on)
    {
        echo "Product/Date Being Worked On - {$product} {$current_date_being_worked_on} \n";
        $save_cache_key_type = env('APP_ENV').'-'.$report_id.'-total-'.$product.'-type-activity-'.$current_date_being_worked_on;

        $product_type_records = Cache::get($save_cache_key_type, []);

        if(count($product_type_records) > 0)
        {
            echo "Found ".count($product_type_records)." product types containing records! \n";

            foreach ($product_type_records as $product_type => $records)
            {
                echo "Product Type Being Worked On - {$product_type} \n";
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
                ];
                $rate = empty($records[0]['pricing_publicondemandrate']) ? 0.00 : $records[0]['pricing_publicondemandrate'];
                $model = DailyProductUsageByType::firstOrCreate($payload);
                $model->desc = $records[0]['lineitem_lineitemdescription'];
                $model->pricing_rate = number_format($rate, 2, '.', '');
                $model->units_used   = number_format($usage_amt, 2, '.', '');
                $model->usage_cost   = number_format($usage_cost, 2, '.', '');
                $model->desc = $records[0]['lineitem_lineitemdescription'];
                $model->save();
            }
        }
    }
}
