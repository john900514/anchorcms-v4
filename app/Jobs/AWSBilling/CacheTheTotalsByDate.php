<?php

namespace App\Jobs\AWSBilling;

use App\Models\AWSBilling\AwsBilling;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CacheTheTotalsByDate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key, $date, $table;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $key, string $date, string $table)
    {
        $this->key = $key;
        $this->date = $date;
        $this->table = $table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AwsBilling $billing)
    {
        $cache_key = $this->key;
        $current_date_being_worked_on = $this->date;
        $billing->changeTable($this->table);
        echo "Caching results from {$this->date} \n";

        try {
            Cache::put($cache_key,
                $billing->getAllRecordsByDate($current_date_being_worked_on),
                (60 * 60) * 2);
        }
        catch(\Exception $e)
        {
            echo "Holy fuck something happened \n";
            dd($e);
        }

    }
}
