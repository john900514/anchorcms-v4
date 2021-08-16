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

class CacheTheTotalProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key, $product, $table;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $key, string $product, string $table)
    {
        $this->key = $key;
        $this->product = $product;
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
        $product = $this->product;
        $billing->changeTable($this->table);
        echo "Caching results from {$this->product} \n";

        try {
            Cache::put($cache_key,
                $billing->getAllRecordsByProduct($product),
                (60 * 60) * 2);
        }
        catch(\Exception $e)
        {
            echo "Holy fuck something happened \n";
            dd($e);
        }

    }
}
