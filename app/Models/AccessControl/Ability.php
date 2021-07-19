<?php

namespace App\Models\AccessControl;

use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Ability as Bility;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Ability extends Bility
{
    use Uuid;

    protected $primaryKey = 'id';

    protected $table = 'abilities';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $connection = 'redfield';

    protected $fillable = ['id', 'name', 'title', 'entity_id', 'entity_type', 'only_owned', 'options', 'scope'];

    protected $casts = [
        'id' => 'string',
        'entity_id' => 'string',
    ];
}
