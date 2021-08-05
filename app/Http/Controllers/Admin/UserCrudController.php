<?php

namespace App\Http\Controllers\Admin;

use App\Aggregates\User\UserActivityAggregate;
use App\Exceptions\User\UserActivityException;
use App\Http\Requests\UserRequest;
use App\Models\AccessControl\Roles;
use App\Models\Clients\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if(Bouncer::is(backpack_user())->an('ad-ops', 'trufit-rep'))
        {
            $this->crud->hasAccessOrFail('nope');
        }
        else
        {
            CRUD::column('name');
            CRUD::column('email');

            CRUD::column('role')->type('closure')
                ->function(function ($entry) {
                    $results = 'Unassigned';
                    $roles = $entry->getRoles();

                    if(count($roles) > 0)
                    {
                        if(count($roles) > 1)
                        {
                            $results = '';
                            foreach ($roles as $idx => $role)
                            {
                                $r = Bouncer::role()->whereName($role)->first();
                                if(!is_null($r))
                                {
                                    if($idx == 0)
                                    {
                                        $results = $r->title;
                                    }
                                    else
                                    {
                                        $results .= ','.$r->title;
                                    }
                                }
                                else
                                {
                                    if($idx == 0)
                                    {
                                        $results = $role;
                                    }
                                    else
                                    {
                                        $results .= ','.$role;
                                    }
                                }
                            }
                        }
                        elseif(Bouncer::is($entry)->a('gm'))
                        {
                            $member_club = $entry->manager_club()->first();

                            if(!is_null($member_club))
                            {
                                $club = Club::whereClubId($member_club->value)
                                    ->first();

                                $v_name = $club->vanilla_html_name()->first();
                                if(!is_null($v_name))
                                {
                                    $results = 'GM - '.$v_name->value." ({$club->club_id})";
                                }
                                else
                                {
                                    $results = 'GM - '.$club->club_name." ({$club->club_id})";
                                }
                            }
                            else
                            {
                                $results = 'GM - no club assigned';
                            }
                        }
                        else
                        {
                            $r = Bouncer::role()->whereName($roles[0])->first();
                            if(!is_null($r))
                            {
                                $results = $r->title;
                            }
                            else
                            {
                                $results = $roles[0];
                            }
                        }
                    }

                    return $results;
                });

            CRUD::column('email_verified_at')->label('Status')
                ->type('closure')->function(function($entry) {
                    $results = 'Unregistered';

                    if(!is_null($entry->deleted_at))
                    {
                        $results = 'Inactive';
                    }
                    elseif(!is_null($entry->email_verified_at))
                    {
                        $results = 'Active';
                    }

                    return $results;
                });

            $this->crud->denyAccess('show');

            //if(Bouncer::is(backpack_user())->an('admin'))
            if(true)
            {
                $this->crud->addClause('select', 'users.*');
                $this->crud->addClause('join', 'assigned_roles', 'assigned_roles.entity_id', '=',
                    //DB::raw('users.id AND assigned_roles.roles_id NOT IN (1)'));
                    DB::raw('users.id AND assigned_roles.role_id NOT IN (1)'));

                $this->crud->addClause('where', 'users.id', '<>', backpack_user()->id);
                CRUD::removeButton('update');
                CRUD::removeButton('delete');
                CRUD::addButtonFromView('line', 'admin-update', 'users.can-admin-edit', 'beginning');
                CRUD::addButtonFromView('line', 'Resend Welcome Email', 'users.resend-email', 'end');
                CRUD::addButtonFromView('line', 'admin-delete', 'users.can-admin-delete', 'end');
            }
            /*
            elseif(Bouncer::is(backpack_user())->an('executive'))
            {
                CRUD::setEntityNameStrings('Staff Member', 'Staff');
                $this->crud->addClause('select', 'users.*');

                $this->crud->addClause('join', 'assigned_roles', 'assigned_roles.entity_id', '=',
                    DB::raw('users.id AND assigned_roles.roles_id IN (4,5,6)'));

                CRUD::removeButton('update');
                CRUD::removeButton('delete');

                CRUD::addButtonFromView('line', 'admin-update', 'users.can-executive-edit', 'beginning');
                CRUD::addButtonFromView('line', 'Resend Welcome Email', 'users.resend-email', 'end');
                CRUD::addButtonFromView('line', 'admin-delete', 'users.can-executive-delete', 'end');
            }
            elseif(Bouncer::is(backpack_user())->an('gm'))
            {
                CRUD::setEntityNameStrings('TruFit Rep/Employee', 'TruFit Reps/Employees');
                $this->crud->addClause('select', 'users.*');

                $this->crud->addClause('join', 'assigned_roles', 'assigned_roles.entity_id', '=',
                    DB::raw('users.id AND assigned_roles.roles_id NOT IN (1, 2, 3, 4, 5)'));

                CRUD::removeButton('delete');
                CRUD::addButtonFromView('line', 'Resend Welcome Email', 'users.resend-email', 'end');
            }
            else
            {
                if(!Bouncer::is(backpack_user())->an('developer'))
                {
                    $this->crud->hasAccessOrFail('nope');
                }
            }
            */
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        if(Bouncer::is(backpack_user())->an('ad-ops'))
        {
            $this->crud->hasAccessOrFail('nope');
        }
        else
        {
            $col_md_6 = ['class' => 'col-md-6'];
            $tz_options = [
                'America/Chicago' => 'Central Standard Time (CST)',
                'America/New_York' => 'Eastern Standard Time (EST)',
                'America/Denver' => 'Mountain Standard Time (MST)',
                'America/Los_Angeles' => 'Pacific Standard Time (PST)',
            ];

            $role_options = Roles::getCRUDRoles();
            $client_options = Client::getCRUDClients();
            $required = ['required' => 'required'];

            $this->crud->addField(['name' => 'name', 'type' => 'text', 'label' => 'Name', 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'email', 'type' => 'email', 'label' => 'Email', 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'details[timezone]', 'type' => 'select_from_array', 'label' => 'Timezone', 'options' => $tz_options, 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'role', 'type' => 'users.select_from_array_roles', 'label' => 'Role', 'options' => $role_options, 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'details[client]',  'type' => 'users.select_from_array_clients', 'label' => 'Client', 'options' => $client_options, 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'details[location]', 'type' => 'users.select_from_array_dept_location', 'label' => 'Department/Location', 'wrapper' => $col_md_6, 'attributes' => $required]);
        }
    }

    public function store()
    {
        $data = $this->crud->getRequest()->request->all();

        $response = $this->traitStore();
        $entry = $this->crud->entry;
        $save_time = date('Y-m-d h:i:s');

        $aggy = UserActivityAggregate::retrieve($entry->id);

        try {
            $aggy = $aggy
                ->createUserHistory($entry->toArray(), backpack_user()->id)
                ->setUsername($entry->name, '', $save_time, backpack_user()->id)
                ->setEmail($entry->email, '', $save_time, backpack_user()->id)
                ->setTimezone($data['details']['timezone'], '', $save_time, backpack_user()->id);
        }
        catch(UserActivityException $e)
        {
            $aggy = $aggy
                ->setUsername($entry->name, '', $save_time, backpack_user()->id)
                ->setEmail($entry->email, '', $save_time, backpack_user()->id)
                ->setTimezone($data['details']['timezone'], '', $save_time, backpack_user()->id);
        }

        // roles!
        $role = $data['role'];
        switch($role)
        {
            case 'developer':
                $aggy = $aggy->setAdminRole($entry->id, $role, $save_time, backpack_user()->id);
            break;

            case 'admin':
            case 'ad-ops':
                $aggy = $aggy->setAdminRole($entry->id, $role, $save_time, backpack_user()->id)
                    ->setAdminDepartment($entry->id, $data['details']['location'],$save_time, backpack_user()->id);
                break;

            default:
                //$aggy = $aggy->setClientRole($entry->id, $role, $save_time, backpack_user()->id);
        }

        $aggy->persist();

        return $response;
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        if(Bouncer::is(backpack_user())->an('ad-ops'))
        {
            $this->crud->hasAccessOrFail('nope');
        }
        else
        {
            $entry = $this->crud->getModel()->find($this->crud->getCurrentEntryId());
            $users_aggy = UserActivityAggregate::retrieve($this->crud->getCurrentEntryId());
            CRUD::setValidation(UserRequest::class);
            $col_md_6 = ['class' => 'col-md-6'];
            $tz_options = [
                'America/Chicago' => 'Central Standard Time (CST)',
                'America/New_York' => 'Eastern Standard Time (EST)',
                'America/Denver' => 'Mountain Standard Time (MST)',
                'America/Los_Angeles' => 'Pacific Standard Time (PST)',
            ];

            $role_options = Roles::getCRUDRoles();
            $client_options = Client::getCRUDClients();
            $required = ['required' => 'required'];

            $this->crud->addField(['name' => 'name', 'type' => 'text', 'label' => 'Name', 'wrapper' => $col_md_6, 'attributes' => $required]);
            $this->crud->addField(['name' => 'email', 'type' => 'email', 'label' => 'Email', 'wrapper' => $col_md_6, 'attributes' => $required]);

            // use the user's aggy to get the last timezone from the history
            $last_tz = $users_aggy->getLastUpdatedTimeZone();
            $this->crud->addField(['name' => 'details[timezone]', 'type' => 'select_from_array', 'label' => 'Timezone', 'options' => $tz_options, 'wrapper' => $col_md_6, 'attributes' => $required, 'value' => $last_tz]);

            $user_role_array = $entry->getRoles();
            $selected_role = (count($user_role_array) > 0) ? $user_role_array[0] : '';

            if(backpack_user()->id != $entry->id)
            {
                // @todo - make sure the user is a dev an admin or has the allowedToChangeRolesAbility
                $this->crud->addField(['name' => 'role', 'type' => 'users.select_from_array_roles', 'label' => 'Role', 'options' => $role_options, 'wrapper' => $col_md_6, 'attributes' => $required, 'value' => $selected_role]);
                $this->crud->addField(['name' => 'details[client]',  'type' => 'users.select_from_array_clients', 'label' => 'Client', 'options' => $client_options, 'wrapper' => $col_md_6, 'attributes' => $required, 'value' => '']);
                $this->crud->addField(['name' => 'details[location]', 'type' => 'users.select_from_array_dept_location', 'label' => 'Department/Location', 'wrapper' => $col_md_6, 'attributes' => $required, 'value' => $users_aggy->getDepartmentLocation()]);
            }
            else
            {
                $attrs = $required;
                $attrs['disabled'] = 'disabled';
                $this->crud->addField(['name' => 'role', 'type' => 'users.select_from_array_roles', 'label' => 'Role', 'options' => $role_options, 'wrapper' => $col_md_6, 'attributes' => $attrs, 'value' => $selected_role, 'hint' => 'You can\'t change your own role.']);
                $this->crud->addField(['name' => 'details[client]',  'type' => 'users.select_from_array_clients', 'label' => 'Client', 'options' => $client_options, 'wrapper' => $col_md_6, 'attributes' => $attrs, 'value' => '']);
                $this->crud->addField(['name' => 'details[location]', 'type' => 'users.select_from_array_dept_location', 'label' => 'Department/Location', 'wrapper' => $col_md_6, 'attributes' => $attrs, 'value' => $users_aggy->getDepartmentLocation()]);
            }

            // @todo - get the user's department if their role is not developer
            //$this->crud->addField(['name' => 'details[location]', 'type' => 'users.select_from_array_dept_location', 'label' => 'Department/Location', 'wrapper' => $col_md_6, 'attributes' => $required, 'value' => $selected_role]);


            $verified = [
                'name' => 'email_verified_at', // the db column name (attribute name)
                'label' => "Completed Registration On", // the human-readable label for it
                'type' => 'text', // the kind of column to show
                'attributes' => [
                    'placeholder' => 'Incomplete',
                    'class' => 'form-control some-class',
                    'readonly'=>'readonly',
                    'disabled'=>'disabled',
                ], // change the HTML attributes of your input
            ];

            $this->crud->addField($verified);

        }
    }

    public function update($id)
    {
        $data = $this->crud->getRequest()->request->all();

        $old_user = User::find($id);

        $response = $this->traitUpdate();
        $entry = $this->crud->getCurrentEntry();
        $save_time = date('Y-m-d h:i:s');

        $aggy = UserActivityAggregate::retrieve($entry->id);

        if($aggy->historyBeenEstablished()) {
                // @todo - use the aggy to get the last known timezone
                $old_tz = '';
                $aggy = $aggy
                    ->setUsername($entry->name, $old_user->name, $save_time, backpack_user()->id)
                    ->setEmail($entry->email, $old_user->email, $save_time, backpack_user()->id)
                    ->setTimezone($data['details']['timezone'], $old_tz, $save_time, backpack_user()->id);
        }
        else
        {
            $old_tz = '';
            try {
                $aggy = $aggy
                    ->createUserHistory($entry->toArray(), backpack_user()->id)
                    ->setUsername($entry->name, $old_user->name, $save_time, backpack_user()->id)
                    ->setEmail($entry->email, $old_user->email, $save_time, backpack_user()->id)
                    ->setTimezone($data['details']['timezone'], $old_tz, $save_time, backpack_user()->id);
            }
            catch(UserActivityException $e)
            {
                // @todo - trigger sentry and maybe an email to the dev in charge of the project
                \Alert::fail('Could not update this user\'s permanent history. Issues may arise now.');
            }
        }

        // roles!
        if(backpack_user()->id != $entry->id)
        {
            // @todo - make sure the user is a dev an admin or has the allowedToChangeRolesAbility
            $role = $data['role'];
            switch($role)
            {
                case 'developer':
                    $aggy = $aggy->setAdminRole($entry->id, $role, $save_time, backpack_user()->id);
                    break;

                case 'admin':
                case 'ad-ops':
                    $aggy = $aggy->setAdminRole($entry->id, $role, $save_time, backpack_user()->id)
                        ->setAdminDepartment($entry->id, $data['details']['location'],$save_time, backpack_user()->id);
                    break;

                default:
                    //$aggy = $aggy->setClientRole($entry->id, $role, $save_time, backpack_user()->id);
            }
        }


        $aggy->persist();

        return $response;
    }
}
