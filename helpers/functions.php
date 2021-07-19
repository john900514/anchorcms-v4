<?php
use Illuminate\Support\Facades\Schema;

if (! function_exists('backpack_users_have_email')) {
    /**
     * Check if the email column is present on the user table.
     *
     * @return string
     */
    function backpack_users_have_email()
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $has = Schema::connection($user->getConn())->hasColumn($user->getTable(), 'email');

        return $has;
    }
}
