<?php

namespace App\Http\Controllers\User;

use Alert;
use Bouncer;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
            $this->data['sentry_auth_token'] = '';
            $sentry_auth_token = backpack_user()->sentry_auth_token()->first();

            if(!is_null($sentry_auth_token))
            {
                $this->data['sentry_auth_token'] = $sentry_auth_token->value;
            }
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

            Alert::success('Sentry Config Info Updated!')->flash();
        }
        else {
            Alert::error('Input a Token to save it!')->flash();
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
