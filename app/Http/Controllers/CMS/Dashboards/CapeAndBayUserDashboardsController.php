<?php

namespace App\Http\Controllers\CMS\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\CnBUserDashboardService;
use Illuminate\Http\Request;

class CapeAndBayUserDashboardsController extends Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(CnBUserDashboardService $service)
    {
        $blade = $service->getDashboardBlade();
        $args = $service->getDashboardArguments();

        return view($blade, $args);
    }
}
