<?php

namespace App\Exceptions\User;

use Exception;

class UserActivityException extends Exception
{
    public static function userAlreadyCreated()
    {
        return new static('User was already created.');
    }

    public static function userAlreadyVerified()
    {
        return new static('User was already verified.');
    }

    public static function accountOwnerOnlyAbility(string $context)
    {
        switch($context)
        {
            case 'password':
                $msg = 'Only the account owner can change their password';
                break;

            default:
                $msg = 'This action is only for the account owner';
        }

        return new static($msg);
    }
}
