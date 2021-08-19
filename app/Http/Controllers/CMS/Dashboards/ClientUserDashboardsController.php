<?php

namespace App\Http\Controllers\CMS\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientUserDashboardsController extends Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(string $client_id)
    {
        $blade = 'errors.coming-soon';
        $args = [];

        return view($blade, $args);
    }
}
