<?php

namespace App\Http\Controllers\User;

use Alert;
use App\Aggregates\User\UserActivityAggregate;
use Bouncer;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Backpack\CRUD\app\Http\Requests\AccountInfoRequest;
use Backpack\CRUD\app\Http\Requests\ChangePasswordRequest;

class UserAccountController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change their personal information & password.
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        if(Bouncer::is(backpack_user())->a('developer'))
        {
            $sentry_auth_token = backpack_user()->sentry_auth_token()->first();
            $this->data['sentry_auth_token'] = (!is_null($sentry_auth_token))
                ? $sentry_auth_token->value
                : '';

            $vault_auth_token  = Cache::get(backpack_user()->id.'-vault-auth-token', backpack_user()->vault_auth_token()->first());
            $this->data['vault_auth_token'] = (!is_null($vault_auth_token))
                ? $vault_auth_token->value
                : '';
        }

        return view('cms.users.user-account', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     * @param AccountInfoRequest $request
     */
    public function postAccountInfoForm(AccountInfoRequest $request)
    {
        $result = $this->guard()->user()->update($request->except(['_token']));

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Save the new password for a user.
     * @param ChangePasswordRequest $request
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Save Developer Sentry Account Info
     * @param Request $request
     */
    public function postChangeSentryForm(Request $request)
    {
        $data = $request->all();

        if(array_key_exists('sentry_auth_token', $data)) {
            $token = $data['sentry_auth_token'];
            $detail_record = backpack_user()->sentry_auth_token()->first();

            if(is_null($detail_record))
            {
                $detail_record = new UserDetails();
                $detail_record->user_id = backpack_user()->id;
                $detail_record->detail = 'sentry_auth_token';
                $detail_record->value = $token;
            }
            else
            {
                $detail_record->value = $token;
            }

            $detail_record->save();
            UserActivityAggregate::retrieve(backpack_user()->id)
                ->setSentryToken($token)
                ->persist();

            Alert::success('Sentry Config Info Updated!')->flash();
        }
        else {
            Alert::error('Input a Token to save it!')->flash();
        }

        return redirect()->back();
    }

    public function postChangeVaultTokenForm(Request $request, UserDetails $details)
    {
        $data = $request->all();
        $user = backpack_user();

        if(array_key_exists('vault_auth_token', $data) && (!is_null($data['vault_auth_token'])))
        {
            // first or create the record
            $payload = [
                'user_id' => $user->id,
                'detail' => '1password-token',
                'active' => true
            ];

            $model = $details->firstOrCreate($payload);
            $model->value = $data['vault_auth_token'];
            $model->save();

            // @todo - Run the aggy, which will trigger the projector and reactor
            UserActivityAggregate::retrieve($user->id)
                ->setVaultToken($data['vault_auth_token'])
                ->persist();

            Alert::success('Successfully updated your Vault Token!')->flash();
        }
        else
        {
            $token = $user->vault_auth_token()->first();

            // If no token exists, alert an error
            if(is_null($token))
            {
                Alert::error('Input a Token to save it and generate your vault access!')->flash();
            }
            else
            {
                // if token exists, deactivate it
                $token->active = false;
                $token->save();

                // @todo - Run the aggy, which will trigger the projector and reactor
                UserActivityAggregate::retrieve($user->id)
                    ->setVaultToken(null)->persist();

                Alert::warning('Your Vault Token was removed. Access to the vault has been removed. To restore access, save a new token')->flash();
            }
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
