<?php

namespace App\Models\Clients;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use CrudTrait, Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $connection = 'redfield';

    protected $fillable = [
        'name',
        'active',
    ];

    public static function getCRUDClients()
    {
        $results = [];

        $client_records = self::whereActive(true)->get();

        if(count($client_records))
        {
            foreach ($client_records as $client_record)
            {
                $results[$client_record->id] = $client_record->name;
            }
        }

        return $results;
    }

    public function details()
    {
        return $this->hasMany('App\Models\Clients\ClientDetail', 'client_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\Clients\ClientDetail', 'client_id', 'id');
    }

    public function uses_umbrella_corp()
    {
        return $this->detail()->whereDetail('uses_umbrella_corp');
    }

    public function website1_url()
    {
        return $this->detail()->whereDetail('website1_url');
    }

    public function segment_id()
    {
        return $this->detail()->whereDetail('segment_id');
    }

    public function sentry_project_hashtag()
    {
        return $this->detail()->whereDetail('sentry_project_hashtag');
    }

    public function uses_allcommerce()
    {
        return $this->detail()->whereDetail('uses_allcommerce');
    }

    public function uses_leadbinder()
    {
        return $this->detail()->whereDetail('uses_leadbinder');
    }

    public function uses_gymrevenue()
    {
        return $this->detail()->whereDetail('uses_gymrevenue');
    }

    public function uses_citycrm()
    {
        return $this->detail()->whereDetail('uses_citycrm');
    }
}
