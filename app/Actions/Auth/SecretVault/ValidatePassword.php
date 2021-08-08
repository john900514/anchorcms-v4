<?php

namespace App\Actions\Auth\SecretVault;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidatePassword
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
            'password' => ['sometimes','required'],
        ];
    }

    public function handle(Request $request)
    {
        $results = ['success' => false];
        $user = auth()->user();
        $data = $request->all();

        if(array_key_exists('password', $data))
        {
            $validated = Cache::remember($user->id.'-vault-access', (60 * 60), function () use($user, $data) {
                $results = false;
                $password = $data['password'];
                if(backpack_auth()->attempt(['email' => $user->email, 'password' => $password]))
                {
                    $results = true;
                }

                return $results;
            });


            if($validated === false)
            {
                $results['reason'] = 'invalid_password';
                Cache::forget($user->id.'-vault-access');

            }
            elseif(is_null($validated))
            {
                $results['reason'] = 'session_expired';
                Cache::forget($user->id.'-vault-access');
            }
            else
            {
                $results['success'] = true;
            }
        }
        else
        {
            $user = is_null($user) ? backpack_user() : $user;
            if((is_null($validated = Cache::get($user->id.'-vault-access'))))
            {
                $results['reason'] = 'missing_password';
                Cache::forget($user->id.'-vault-access');
            }
            else
            {
                $results['success'] = true;
            }

        }

        return $results;
    }

    public function jsonResponse($result, $request)
    {
        $code = 500;

        if($result['success'])
        {
            $code = 200;
        }
        else
        {
            $result['code'] = $result['reason'];
            switch($result['code'])
            {
                case 'missing_password':

                    $result['reason'] = 'Missing Password. Try again.';
                    break;

                case 'invalid_password':
                    $result['reason'] = 'Invalid Password. Try again.';
                    break;

                case 'session_expired':
                    $result['reason'] = 'Invalid Password Your Session Has Expired. Enter your password again.';
            }
        }

        return response($result, $code);
    }
}
