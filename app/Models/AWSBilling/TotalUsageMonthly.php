<?php

namespace App\Models\AWSBilling;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TotalUsageMonthly extends Model
{
    use SoftDeletes, Uuid;

    protected $connection = 'aws-billing';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['date', 'usage_cost'];
}
