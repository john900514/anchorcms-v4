<?php

namespace App\Http\Controllers\User;

use App\Actions\User\CompleteRegistration;
use App\Aggregates\User\UserActivityAggregate;
use App\Exceptions\User\UserActivityException;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UserRegistrationController extends Controller
{
    protected $request, $users;

    public function __construct(Request $request, User $users)
    {
        $this->request = $request;
        $this->users = $users;
    }

    public function render_complete_registration()
    {
        if($this->request->has('session'))
        {
            $new_user = $this->users->find($this->request->get('session'));

            if(!is_null($new_user))
            {
                if((is_null($new_user->email_verified_at)))
                {
                    auth()->logout();
                    auth()->login($new_user);

                    $role_slug = $new_user->getRoles()[0];
                    $role = Bouncer::role()->whereName($role_slug)->first()['title'];

                    if(is_null($role)) { $role = ''; }

                    $args = [
                        'user' => $new_user,
                        'role' => $role
                    ];

                    return view('cms.users.registration.complete-cnb-user-registration', $args);
                }
                else
                {
                    return $this->redirectUnqualifiedUser($new_user);
                }
            }
            else
            {
                return view('errors.404');
            }
        }
        else
        {
            return $this->redirectUnqualifiedUser(backpack_user());
        }
    }

    public function complete_registration()
    {
        $data = $this->request->all();

        $validated = Validator::make($data, [
            'session_token' => 'bail|required|exists:redfield.users,id',
            'name' => 'bail|required',
            'email' => 'bail|required|email:rfc,dns',
            'password' => 'bail|required',
            'password_confirmation' => 'bail|required'
        ]);

        if ($validated->fails())
        {
            $errors = [];
            foreach($validated->errors()->toArray() as $idx => $error_msg)
            {
                session()->put('status', 'There was a problem with your submission. Please Try Again.');
                $errors[$idx] = $error_msg[0];
            }

            return redirect()->back()->withErrors($errors);
        }
        else
        {
            if($data['password'] == $data['password_confirmation'])
            {
                $user = $this->users->find($data['session_token']);
                backpack_auth()->login($user);
                $save_date = date('Y-m-d h:i:s');

                try {
                    $aggy = UserActivityAggregate::retrieve($data['session_token'])
                        ->setUserVerified($save_date)
                        ->setPassword($data['password'], $save_date, $user->id)
                        ->setUsername($data['name'], $user->name, $save_date, $user->id)
                        ->setEmail($data['email'], $user->email, $save_date, $user->id);
                }
                catch(UserActivityException $e)
                {
                    session()->put('status', 'There was a problem with your submission. Please Try Again.');
                    return redirect()->back()->withErrors([
                        'password' => $e->getMessage().' Try clearing your cookies.',
                    ]);
                }

                if($aggy->persist())
                {
                    backpack_auth()->logout();
                    session()->put('status', 'Registration Success! Login to Continue.');
                    \Alert::success('Registration Success! Login to Continue.')->flash();
                    return redirect('access');
                }
                else
                {
                    session()->put('status', 'There was a problem saving your data. Please Try Again.');
                    return redirect()->back();
                }

            }
            else
            {
                session()->put('status', 'There was a problem with your submission. Please Try Again.');
                return redirect()->back()->withErrors([
                    'password' => 'Must Match the Confirm new password',
                    'password_confirmation' => 'Must Match the password'
                ]);
            }
        }
    }

    private function redirectUnqualifiedUser(User $user)
    {
        if($user = backpack_user())
        {
            return redirect('dashboard');
        }
        else
        {
            return redirect('/');
        }
    }
}
