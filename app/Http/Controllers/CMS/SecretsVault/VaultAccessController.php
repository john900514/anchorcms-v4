<?php

namespace App\Http\Controllers\CMS\SecretsVault;

use App\Actions\Auth\SecretVault\ValidatePassword;
use App\Http\Controllers\Controller;
use App\Models\Clients\LocationDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaultAccessController extends Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $blade = 'errors.403';
        $args = [];

        if(backpack_user()->can('view-secrets-vault'))
        {
            // get the user's 1password-token
            $vault_access_record = backpack_user()->vault_auth_token()->first();
            if(!is_null($vault_access_record))
            {
                // it also must be base64_decoded
                $args['vault_token'] = base64_decode($vault_access_record->value);
                // Get the user's Auth token
                Auth::shouldUse('api');
                auth()->login(backpack_user());
                $args['auth_token'] = auth()->refresh();

                $validated = ValidatePassword::run($this->request);
                if($validated['success'])
                {
                    $args['session'] = 'logged_in';
                }

                $blade = 'cms.vault.entrance';
            }
            else
            {
                $blade = 'errors.401';
            }

        }

        return view($blade, $args);
    }

    public function vault_list()
    {
        $blade = 'errors.419';
        $args = [];

        $validated = ValidatePassword::run($this->request);
        if($validated['success'])
        {
            \Alert::add('secondary', 'Warning - Refreshing can break your session. Attempting to restore.')->flash();
            return redirect()->route('secret-vault');
        }


        return view($blade, $args);
    }
}
