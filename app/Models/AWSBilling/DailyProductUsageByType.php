<?php

namespace App\Models\AWSBilling;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyProductUsageByType extends Model
{
    use SoftDeletes, Uuid;

    protected $connection = 'aws-billing';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['date', 'product', 'usage_type', 'desc', 'pricing_rate', 'usage_cost', 'resource_id', 'product_region','client_id'];
}
