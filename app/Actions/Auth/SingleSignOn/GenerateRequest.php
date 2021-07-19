<?php

namespace App\Actions\Auth\SingleSignOn;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SingleSignOnService;
use Lorisleiva\Actions\Concerns\AsAction;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Lorisleiva\Actions\Concerns\AsController;

class GenerateRequest
{
    use  AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {

    }

    public function rules(): array
    {
        // Validate the user and the location
        return [
            'userId' => ['required', 'exists:redfield.users,id'],
            'location' => ['required'],
        ];
    }

    public function handle(Request $request, SingleSignOnService $sso)
    {
        $results = false;
        $data = $request->all();

        $sso_info = $sso->getIntegrationWithSlug($data['location']);

        if($sso_info)
        {

            $role_access = (is_null($sso_info['permitted_role']) || Bouncer::is(backpack_user())->an($sso_info['permitted_role']));
            $ability_access = (is_null($sso_info['permitted_ability']) || backpack_user()->can($sso_info['permitted_ability'], $sso_info['client_id']));
            if($role_access || $ability_access)
            {
                Auth::shouldUse('api');
                auth()->login(backpack_user());
                $token = auth()->refresh();

                $jwt_payload = [
                    'user' => backpack_user()->id,
                    'destination' => $sso_info['default_route'],
                    "exp" => strtotime('NOW  +5 MIN'),
                    "iat" => strtotime('NOW')
                ];

                $private_key = base64_decode($sso_info['private_key']);
                $jwt = JWT::encode($jwt_payload, $private_key, 'RS256');

                $results = [
                    'token' => $token,
                    'jwt' => $jwt,
                    'url' => $sso_info['base_url'].$sso_info['default_route'],
                    'uuid' => $sso_info['id']
                ];
            }

        }
        else
        {
            $results = 'Location is not integrated';
        }

        return $results;
    }

    public function htmlResponse($result)
    {
        if($result)
        {
            if(is_array($result))
            {
                $url = "{$result['url']}?token={$result['jwt']}&session={$result['token']}&uuid={$result['uuid']}";
                $results = redirect($url);
            }
            else
            {
                \Alert::error($result);
                $results = redirect()->back();
            }
        }
        else
        {
            $results = view('errors.404');
        }

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = ['success' => false, 'reason' => 'Invalid Request'];
        $code = 500;

        if($result)
        {
            if(is_array($result))
            {
                $results = ['success' => true, 'result' => $result];
                $code = 200;
            }
            else
            {
                $results['reason'] = $result;
                $code = 401;
            }
        }

        return response($results, $code);
    }
}
