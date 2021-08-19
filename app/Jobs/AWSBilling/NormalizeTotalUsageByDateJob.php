<?php

namespace App\Jobs\AWSBilling;

use App\Models\AWSBilling\AwsBilling;
use App\Models\AWSBilling\TotalUsageDaily;
use App\Models\AWSBilling\TotalUsageMonthly;
use App\Models\Data\Reports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class NormalizeTotalUsageByDateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $report_id, $total_usage_cost;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $report_id)
    {
        $this->report_id = $report_id;
        $this->total_usage_cost = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Reports $report_model, AwsBilling $billing, TotalUsageDaily $usage_model, TotalUsageMonthly $total_usage_model)
    {
        $report = Cache::get($this->report_id, $report_model->find($this->report_id));
        $table_date = $report->misc['table_date'];
        $billing->changeTable($table_date);
        $newest_record = Cache::get($this->report_id.'-newest', $billing->orderBy('identity_timeinterval', 'DESC')->first());

        $end_date = date('Y-m-d', strtotime($newest_record->lineitem_usagestartdate));
        $first_date = date('Y-m-', strtotime($newest_record->lineitem_usagestartdate)).'01';
        $current_date_being_worked_on = $first_date;

        while($current_date_being_worked_on != $end_date)
        {
            echo "Date Being Normalized On - {$current_date_being_worked_on} \n";
            $this->processTheThing($current_date_being_worked_on, $billing, $usage_model);

            $current_date_being_worked_on = date('Y-m-d', strtotime("$current_date_being_worked_on +1DAY"));
        }

        if($current_date_being_worked_on == $end_date)
        {
            echo "Final Date Being Normalized On - {$current_date_being_worked_on} \n";
            $this->processTheThing($current_date_being_worked_on, $billing, $usage_model);
        }

        echo "Saving Monthly Total for everything...how lame...\n";
        $payload = [
            'date' => date('Y-m', strtotime($report->misc['table_date'])),
        ];

        $model = $total_usage_model->firstOrCreate($payload);
        $model->usage_cost = number_format($this->total_usage_cost, 2, '.', '');
        $model->save();
    }

    private function processTheThing(string $current_date_being_worked_on, AwsBilling $billing, TotalUsageDaily $usage_model)
    {
        $cache_key = env('APP_ENV').'-'.$this->report_id.'-total_activity-'.$current_date_being_worked_on;
        $current_days_records = Cache::get($cache_key, $billing->getAllRecordsByDate($current_date_being_worked_on));

        if(count($current_days_records) > 0)
        {
            echo "Found ".count($current_days_records)." records to add up! \n";
            $usage_cost = 0;
            foreach ($current_days_records as $record)
            {
                $usage_cost += $record['pricing_publicondemandcost'];
                $this->total_usage_cost += $record['pricing_publicondemandcost'];
            }

            $payload = [
                'date' => $current_date_being_worked_on,
            ];
            $model = $usage_model->firstOrCreate($payload);
            $model->usage_cost = number_format($usage_cost, 2, '.', '');
            $model->save();

            // @todo - Call the AWSBillingReportAggregate aggy and log this shit
        }
    }
}
