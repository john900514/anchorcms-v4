<?php

namespace App\Actions\Auth\Dashboards;

use App\Http\Controllers\CMS\Dashboards\CapeAndBayUserDashboardsController;
use App\Http\Controllers\CMS\Dashboards\ClientUserDashboardsController;
use App\Services\CnBUserDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadDashboardController
{
    use  AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {

    }

    public function handle(Request $request)
    {
        $user = backpack_user();
        $is_client = Cache::remember($user->id.'-is_client_user', (60 * 60) * 24, function () use($user) {
            $results = false;
            $client_detail_model = $user->is_client_user()->first();

            if(!is_null($client_detail_model))
            {
                $results = $client_detail_model->value;
            }

            return $results;
        });

        $controller = ($is_client)
            ? new ClientUserDashboardsController($request)
            : new CapeAndBayUserDashboardsController($request);

        $service_load_in = ($is_client)
            ? null
            : new CnBUserDashboardService();

        return($is_client)
            ? $controller->index($is_client)
            : $controller->index($service_load_in);
    }
}
