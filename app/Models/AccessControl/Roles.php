<?php

namespace App\Models\AccessControl;

use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class Roles extends Role
{
    protected $primaryKey = 'id';

    protected $table = 'role';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $connection = 'redfield';

    public static function getCRUDRoles()
    {
        if(Bouncer::is(backpack_user())->a('developer'))
        {
            $role_options = [
                'developer' => 'Developer',
                'admin' => 'Cape & Bay Admin',
                'ad-ops' => 'Cape & Bay Ad-Ops',
                'executive' => 'TruFit Executive',
                'gm' => 'TruFit Club General Manager',
                'trufit-rep' => 'TruFit Employee Rep',
            ];
        }
        elseif(Bouncer::is(backpack_user())->an('admin'))
        {
            $role_options = [
                'admin' => 'Cape & Bay Admin',
                'ad-ops' => 'Cape & Bay Ad-Ops',
                'executive' => 'TruFit Executive',
                'gm' => 'TruFit Club General Manager',
                'trufit-rep' => 'TruFit Employee Rep',
            ];
        }
        elseif(Bouncer::is(backpack_user())->an('executive'))
        {
            $role_options = [
                'executive' => 'TruFit Executive',
                'gm' => 'TruFit Club General Manager',
                'trufit-rep' => 'TruFit Employee Rep',
            ];
        }
        else
        {
            $role_options = [
                'trufit-rep' => 'TruFit Employee Rep',
            ];
        }

        return $role_options;
    }
}
