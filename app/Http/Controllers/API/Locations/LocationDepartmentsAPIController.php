<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;
use App\Models\Clients\LocationDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationDepartmentsAPIController extends Controller
{
    protected Request $request;
    protected LocationDepartment $model;

    public function __construct(Request $request, LocationDepartment $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    public function index()
    {

    }

    public function show($client_id = null)
    {
        $results = [];
        $code = 200;

        if($client_id == 'cnb')
        {
            $model = $this->model;
            $records = Cache::remember('all-active-departments', (60 * 60)* 2, function () use($model) {
                return $model->whereNull('client_id')
                    ->whereActive(true)
                    ->get();
            });

        }
        else
        {
            $model = $this->model;
            $records = Cache::remember('client-'.$client_id.'-active-locations', (60 * 60)* 2, function () use($model, $client_id) {
                return $model->whereClientId($client_id)
                    ->whereActive(true)
                    ->get();
            });

        }

        if(count($records) > 0)
        {
            foreach ($records as $record)
            {
                // @todo - do some validation here
                $results[$record->slug] = $record->name;
            }
        }
        else
        {
            $results['reason'] = 'No results';
            $code = 500;
        }

        return response($results, $code);
    }
}
