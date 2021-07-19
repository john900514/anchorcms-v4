<?php

namespace App\Http\Controllers\API\Integrations;

use App\Http\Controllers\Controller;
use App\Models\Auth\SsoIntegrations;
use Illuminate\Http\Request;

class SSOIntegrationsAPIController extends Controller
{
    protected $request, $sso_model;

    public function __construct(Request $request, SsoIntegrations $sso)
    {
        $this->middleware('auth:api');
        $this->request = $request;
        $this->sso_model = $sso;
    }

    public function index()
    {
        $results = ['success' => false, 'reason' => 'You do not have access to this resource'];
        $code = 401;

        $user = auth()->user();

        if($user->can('manage', $this->sso_model))
        {
            $integrations = $this->sso_model->whereActive(1)->get();

            if(count($integrations) > 0)
            {
                $r = [];
                foreach($integrations as $idx => $integration)
                {
                    $r[$idx] = $integration->toArray();
                    unset($r[$idx]['private_key']);
                    unset($r[$idx]['public_key']);
                    unset($r[$idx]['default_route']);
                    unset($r[$idx]['updated_at']);
                    unset($r[$idx]['deleted_at']);
                    unset($r[$idx]['permitted_role']);
                    unset($r[$idx]['permitted_ability']);
                    //$r[$idx]['public_key'] = base64_decode($r[$idx]['public_key']);
                }

                $results = ['success' => true, 'integrations' => $r];
                $code = 200;
            }
            else
            {
                $results['reason'] = 'No Integrations available';
                $code = 200;
            }
        }
        else
        {
            // @todo - only send back integrations with permitted role or ability
        }

        return response($results, $code);
    }

    public function show(string $integration_id)
    {
        $results = ['success' => false, 'reason' => 'You do not have access to this resource'];
        $code = 401;

        $user = auth()->user();

        if($user->can('manage', $record = $this->sso_model->find($integration_id)))
        {
            $r = $record->toArray();
            $data = $this->request->all();

            // @todo - check the header in the request to make sure a service header exists and is the same as service_id in record.
            // @todo - neither side can be null or fail with a 401
            if(array_key_exists('public_key', $data) && $data['public_key'])
            {
                $results = ['success' => true, 'public_key' => base64_decode($r['public_key'])];
            }
            else
            {
                unset($r['private_key']);
                unset($r['public_key']);
                unset($r['default_route']);
                unset($r['updated_at']);
                unset($r['deleted_at']);
                unset($r['permitted_role']);
                unset($r['permitted_ability']);

                $results = ['success' => true, 'integration' => $r];
            }

            $code = 200;
        }

        return response($results, $code);
    }
}
